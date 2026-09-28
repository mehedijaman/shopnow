/**
 * Shared translation runtime for both Vite surfaces.
 *
 * `php artisan localization:export` flattens the PHP lang files into one JSON
 * catalogue per locale and surface:
 *
 *   resources/js/lang/{locale}.json      admin (Inertia) surface
 *   resources-site/js/lang/{locale}.json storefront surface
 *
 * Each surface lazy-imports only the bundle for the locale declared on
 * `<html lang="...">`, so a page ships a single catalogue. Keys keep the exact
 * server-side shape ("common.buttons.save", "cart::site.title") so a key that
 * resolves in PHP resolves here too.
 *
 * `choose()` and `makeReplacements()` are ports of
 * Illuminate\Translation\MessageSelector and Translator::makeReplacements().
 */

/**
 * Plural index rules, mirroring MessageSelector::getPluralIndex(). Both
 * supported locales use the "one" rule: 1 selects the first segment. Add an
 * entry here when a locale with a different rule is added to
 * config/localization.php.
 */
const PLURAL_RULES = {
    en: (number) => (number === 1 ? 0 : 1),
    bn: (number) => (number === 1 ? 0 : 1)
}

const pluralIndex = (locale, number) => {
    const language = String(locale).split(/[_-]/)[0]
    const rule = PLURAL_RULES[language] ?? PLURAL_RULES.en

    return rule(Number(number))
}

/**
 * Compare the inline condition of a segment against the number, using the
 * loose semantics of PHP's `==`.
 */
const conditionMatches = (condition, number) => {
    const numeric = Number(condition)

    if (condition.trim() !== '' && Number.isFinite(numeric)) {
        return numeric === Number(number)
    }

    return String(condition) === String(number)
}

const extractFromString = (part, number) => {
    const matches = part.match(/^[\{\[]([-?\d|*,\.*]*)[\}\]]([\s\S]*)$/)

    if (!matches) {
        return null
    }

    const condition = matches[1]
    const value = matches[2]

    if (condition.includes(',')) {
        const separator = condition.indexOf(',')
        const from = condition.slice(0, separator)
        const to = condition.slice(separator + 1)

        if (to === '*' && Number(number) >= Number(from)) {
            return value
        }

        if (from === '*' && Number(number) <= Number(to)) {
            return value
        }

        if (Number(number) >= Number(from) && Number(number) <= Number(to)) {
            return value
        }

        return null
    }

    return conditionMatches(condition, number) ? value : null
}

const extract = (segments, number) => {
    for (const segment of segments) {
        const value = extractFromString(segment, number)

        if (value !== null) {
            return value
        }
    }

    return null
}

/**
 * Select the segment of a pluralized line for the given number.
 */
export const choose = (line, number, locale) => {
    const segments = String(line).split('|')
    const extracted = extract(segments, number)

    if (extracted !== null) {
        return extracted.trim()
    }

    const texts = segments.map((segment) =>
        segment.replace(/^[\{\[][-?\d|*,\.*]*[\}\]]/, '')
    )
    const index = pluralIndex(locale, number)

    if (texts.length === 1 || texts[index] === undefined) {
        return texts[0]
    }

    return texts[index]
}

/**
 * Replace `:key`, `:Key` and `:KEY` placeholders in a single pass, matching
 * the longest key first the way PHP's strtr() does.
 */
export const makeReplacements = (line, replace) => {
    if (!replace) {
        return line
    }

    const map = new Map()

    for (const [key, value] of Object.entries(replace)) {
        if (value === null || value === undefined) {
            continue
        }

        const text = String(value)

        map.set(`:${key}`, text)
        map.set(
            `:${key.charAt(0).toUpperCase()}${key.slice(1)}`,
            text.charAt(0).toUpperCase() + text.slice(1)
        )
        map.set(`:${key.toUpperCase()}`, text.toUpperCase())
    }

    if (map.size === 0) {
        return line
    }

    return line.replace(/:[A-Za-z0-9_]+/g, (token) => {
        for (let length = token.length; length > 1; length--) {
            const candidate = token.slice(0, length)

            if (map.has(candidate)) {
                return map.get(candidate) + token.slice(length)
            }
        }

        return token
    })
}

const detectLocale = () => {
    const declared = document.documentElement.getAttribute('lang') || ''

    return declared.split(/[_-]/)[0] || 'en'
}

/**
 * Build a translator over a Vite `import.meta.glob()` result.
 *
 * @param {object} options
 * @param {Record<string, () => Promise<{default?: Record<string, string>}>>} options.bundles
 * @param {(locale: string) => string} options.pathFor bundle key for a locale
 * @param {string} [options.fallbackLocale]
 */
export const createTranslator = ({
    bundles,
    pathFor,
    fallbackLocale = 'en'
}) => {
    let catalogue = {}
    let locale = fallbackLocale

    const load = async (requested = detectLocale()) => {
        const requestedPath = pathFor(requested)
        const fallbackPath = pathFor(fallbackLocale)
        const path = bundles[requestedPath] ? requestedPath : fallbackPath

        locale = bundles[requestedPath] ? requested : fallbackLocale

        try {
            const module = await (bundles[path]
                ? bundles[path]()
                : Promise.resolve({}))

            catalogue = module.default ?? module ?? {}
        } catch (error) {
            console.error(`Failed to load translation bundle [${path}]`, error)
            catalogue = {}
        }

        return catalogue
    }

    const translate = (key, replace = {}) => {
        const line = Object.prototype.hasOwnProperty.call(catalogue, key)
            ? catalogue[key]
            : key

        const count = replace && replace.count

        if (
            count !== undefined &&
            count !== null &&
            Number.isFinite(Number(count))
        ) {
            return makeReplacements(
                choose(line, Number(count), locale),
                replace
            )
        }

        return makeReplacements(line, replace)
    }

    return {
        load,
        translate,
        get locale() {
            return locale
        }
    }
}

/**
 * Vue plugin wiring: `__()` in templates and `$translate()` via injection.
 *
 * @param {{translate: (key: string, replace?: object) => string}} translator
 */
export const createTranslationsPlugin = (translator) => ({
    install: (app) => {
        app.config.globalProperties.__ = translator.translate
        app.provide('translate', translator.translate)
    }
})
