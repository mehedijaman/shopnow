import vue from 'eslint-plugin-vue'
import vueParser from 'vue-eslint-parser'
import prettierConfig from 'eslint-config-prettier'

/**
 * Names the glossary marks as never translated (brands, couriers, social
 * networks). They are deliberately left as raw text rather than wrapped in
 * `__()`, so the rule has to be told about them.
 */
const NEVER_TRANSLATED = [
    'ShopNow',
    'Pathao',
    'Steadfast',
    'RedX',
    'eCourier',
    'Paperfly',
    'Facebook',
    'X',
    'Instagram',
    'YouTube',
    'LinkedIn',
    'TikTok',
    'GitHub',
    'WhatsApp'
]

/**
 * Text sitting directly in a template is invisible to `localization:check`,
 * which can only see calls that go through `__()`. Shipped as a local rule
 * because eslint-plugin-vue has no equivalent and adding an i18n plugin is not
 * worth a new dependency.
 */
const localization = {
    rules: {
        'no-raw-text': {
            meta: {
                type: 'problem',
                docs: {
                    description:
                        'Disallow raw text in templates so every string can be found by localization:check'
                },
                messages: {
                    rawText:
                        'Raw text "{{ text }}" cannot be translated; wrap it in __().'
                }
            },
            create(context) {
                const parserServices =
                    context.sourceCode?.parserServices ?? context.parserServices

                if (!parserServices?.defineTemplateBodyVisitor) {
                    return {}
                }

                return parserServices.defineTemplateBodyVisitor({
                    VText(node) {
                        const text = node.value

                        // Numbers, punctuation and symbols are not translated.
                        if (!/\p{L}/u.test(text)) {
                            return
                        }

                        if (text.trim() === '') {
                            return
                        }

                        if (NEVER_TRANSLATED.includes(text.trim())) {
                            return
                        }

                        context.report({
                            node,
                            loc: node.loc,
                            messageId: 'rawText',
                            data: { text: text.trim().replace(/\s+/g, ' ') }
                        })
                    }
                })
            }
        }
    }
}

export default [
    // Vue plugin configuration
    {
        files: ['**/*.vue'],
        languageOptions: {
            parser: vueParser,
            parserOptions: {
                ecmaVersion: 'latest',
                sourceType: 'module'
            }
        },
        plugins: {
            vue,
            localization
        },
        rules: {
            // Combine base and recommended Vue rules
            ...vue.configs.base.rules,
            ...vue.configs['recommended'].rules,

            // Disable specific Vue rules
            'vue/no-v-html': 'off',
            'vue/comment-directive': 'off',

            // Warn only for now: there is existing markup to migrate.
            'localization/no-raw-text': 'warn'

            // You can add other Vue-specific rules here
        }
    },

    // General JavaScript rules (for .js and .vue files)
    {
        files: ['**/*.{js,vue}'],
        rules: {
            // Disable general ESLint rules
            // 'no-undef': 'off'
        }
    },

    // Prettier configuration to disable conflicting rules
    {
        rules: {
            ...prettierConfig.rules
        }
    },

    // Custom rules (if any)
    {
        languageOptions: {
            globals: {
                document: 'readonly',
                window: 'readonly',
                FileReader: 'readonly',
                FormData: 'readonly',
                URLSearchParams: 'readonly',
                localStorage: 'readonly',
                fetch: 'readonly',
                alert: 'readonly',
                console: 'readonly',
                route: 'readonly',
                Ziggy: 'readonly'
            }
        },
        rules: {
            // Add your custom rules here
        }
    },

    // Ignore patterns
    {
        ignores: ['node_modules/*', 'vendor/*', 'public/*']
    }
]
