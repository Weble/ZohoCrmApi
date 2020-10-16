const {description} = require('../../package')

module.exports = {
    /**
     * Ref：https://v1.vuepress.vuejs.org/config/#title
     */
    title: 'Zoho CRM Api - PHP SDK - Documentation',
    /**
     * Ref：https://v1.vuepress.vuejs.org/config/#description
     */
    description: description,

    /**
     * Extra tags to be injected to the page HTML `<head>`
     *
     * ref：https://v1.vuepress.vuejs.org/config/#head
     */
    head: [
        ['meta', {name: 'theme-color', content: '#3eaf7c'}],
        ['meta', {name: 'apple-mobile-web-app-capable', content: 'yes'}],
        ['meta', {name: 'apple-mobile-web-app-status-bar-style', content: 'black'}]
    ],

    /**
     * Theme configuration, here is the default theme configuration for VuePress.
     *
     * ref：https://v1.vuepress.vuejs.org/theme/default-theme-config.html
     */
    themeConfig: {
        repo: 'https://github.com/Weble/ZohoCrmApi',
        editLinks: true,
        docsDir: '',
        editLinkText: 'Edit on Github',
        lastUpdated: false,
        nav: [
            {
                text: 'Documentation',
                link: '/',
            },
            {
                text: 'Github',
                link: 'https://github.com/Weble/ZohoCrmApi'
            },
            {
                text: 'About Us',
                link: 'https://weble.it'
            }
        ],
        sidebar: {
            '/': [
                {
                    title: 'Documentation',
                    collapsable: false,
                    children: [
                        '',
                        'installation',
                        'configuration',
                        'usage'
                    ]
                }
            ],
        }
    },

    /**
     * Apply plugins，ref：https://v1.vuepress.vuejs.org/zh/plugin/
     */
    plugins: [
        '@vuepress/plugin-back-to-top',
        '@vuepress/plugin-medium-zoom',
    ]
}
