import { translate } from '@/Plugins/Translations'

export default {
    // main navigation - side menu
    items: [
        {
            get label() {
                return translate('common.menu.dashboard')
            },
            permission: 'Dashboard',
            icon: 'ri-dashboard-line',
            link: route('dashboard.index')
        },
        {
            get label() {
                return translate('common.menu.contact_messages')
            },
            permission: 'contact-message-list',
            icon: 'ri-mail-line',
            link: route('contactMessage.index')
        },
        {
            get label() {
                return translate('common.menu.order_management')
            },
            permission: 'order-menu',
            children: [
                {
                    get label() {
                        return translate('common.menu.order_list')
                    },
                    permission: 'order-list',
                    icon: 'ri-draft-line',
                    link: route('order.index')
                },
                {
                    get label() {
                        return translate('common.menu.create_order')
                    },
                    permission: 'order-create',
                    icon: 'ri-add-line',
                    link: route('order.create')
                },
                {
                    get label() {
                        return translate('common.menu.order_report')
                    },
                    permission: 'order-list',
                    icon: 'ri-bar-chart-2-line',
                    link: route('order.report')
                },
                {
                    get label() {
                        return translate('common.menu.promo_codes')
                    },
                    permission: 'promo-code-list',
                    icon: 'ri-coupon-3-line',
                    link: route('promoCode.index')
                }
            ]
        },

        {
            get label() {
                return translate('common.menu.product_management')
            },
            permission: 'product-menu',
            children: [
                {
                    get label() {
                        return translate('common.menu.new_product')
                    },
                    permission: 'product-create',
                    icon: 'ri-add-line',
                    link: route('product.create')
                },
                {
                    get label() {
                        return translate('common.menu.products')
                    },
                    permission: 'product-list',
                    icon: 'ri-draft-line',
                    link: route('product.index')
                },
                {
                    get label() {
                        return translate('common.menu.product_report')
                    },
                    permission: 'product-list',
                    icon: 'ri-bar-chart-2-line',
                    link: route('product.report')
                },
                {
                    get label() {
                        return translate('common.menu.product_categories')
                    },
                    permission: 'product-category-list',
                    icon: 'ri-folders-line',
                    link: route('productCategory.index')
                },
                {
                    get label() {
                        return translate('common.menu.product_tags')
                    },
                    permission: 'product-tag-list',
                    icon: 'ri-price-tag-3-line',
                    link: route('productTag.index')
                },
                {
                    get label() {
                        return translate('common.menu.product_brands')
                    },
                    permission: 'product-brand-list',
                    icon: 'ri-team-line',
                    link: route('productBrand.index')
                },
                {
                    get label() {
                        return translate('common.menu.product_attributes')
                    },
                    permission: 'product-attribute-list',
                    icon: 'ri-list-settings-line',
                    link: route('productAttribute.index')
                }
            ]
        },

        {
            get label() {
                return translate('common.menu.customer_management')
            },
            permission: 'customer-menu',
            children: [
                {
                    get label() {
                        return translate('common.menu.customers')
                    },
                    permission: 'customer-list',
                    icon: 'ri-draft-line',
                    link: route('customer.index')
                },
                {
                    get label() {
                        return translate('common.menu.customer_report')
                    },
                    permission: 'customer-list',
                    icon: 'ri-bar-chart-2-line',
                    link: route('customer.report')
                }
            ]
        },

        {
            get label() {
                return translate('common.menu.blog')
            },
            permission: 'Blog',
            children: [
                {
                    get label() {
                        return translate('common.menu.posts')
                    },
                    permission: 'Blog: Post - List',
                    icon: 'ri-draft-line',
                    link: route('blogPost.index')
                },
                {
                    get label() {
                        return translate('common.menu.categories')
                    },
                    permission: 'Blog: Category - List',
                    icon: 'ri-folders-line',
                    link: route('blogCategory.index')
                },
                {
                    get label() {
                        return translate('common.menu.tags')
                    },
                    permission: 'Blog: Tag - List',
                    icon: 'ri-price-tag-3-line',
                    link: route('blogTag.index')
                },
                {
                    get label() {
                        return translate('common.menu.authors')
                    },
                    permission: 'Blog: Author - List',
                    icon: 'ri-team-line',
                    link: route('blogAuthor.index')
                }
            ]
        },

        {
            get label() {
                return translate('common.menu.sliders')
            },
            permission: 'slider-list',
            icon: 'ri-image-line',
            link: route('slider.index')
        },

        {
            get label() {
                return translate('common.menu.pages')
            },
            permission: 'page-list',
            icon: 'ri-pages-line',
            link: route('page.index')
        },

        {
            get label() {
                return translate('common.menu.access_control_list')
            },
            permission: 'Acl',
            children: [
                {
                    get label() {
                        return translate('common.menu.users')
                    },
                    permission: 'Acl: User - List',
                    icon: 'ri-user-line',
                    link: route('user.index')
                },
                {
                    get label() {
                        return translate('common.menu.permissions')
                    },
                    permission: 'Acl: Permission - List',
                    icon: 'ri-shield-keyhole-line',
                    link: route('aclPermission.index')
                },
                {
                    get label() {
                        return translate('common.menu.roles')
                    },
                    permission: 'Acl: Role - List',
                    icon: 'ri-account-box-line',
                    link: route('aclRole.index')
                }
            ]
        },

        {
            get label() {
                return translate('common.menu.settings')
            },
            permission: 'settings-list',
            icon: 'ri-settings-3-line',
            link: route('settings.show', { group: 'general' })
        },

        {
            get label() {
                return translate('common.menu.my_profile')
            },
            icon: 'ri-user-settings-line',
            get link() {
                return route('profile.show')
            }
        }
    ]
}
