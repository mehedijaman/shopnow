import {
    createTranslator,
    createTranslationsPlugin
} from '../../../resources/js/Plugins/TranslationCore'

const translator = createTranslator({
    bundles: import.meta.glob('../lang/*.json'),
    pathFor: (locale) => `../lang/${locale}.json`
})

/**
 * Load the catalogue for the locale declared on `<html lang>`. Must settle
 * before the Vue app mounts (see createVueApp()).
 */
export const loadTranslations = () => translator.load()

export const translate = translator.translate

export default createTranslationsPlugin(translator)
