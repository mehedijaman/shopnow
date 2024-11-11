export default {
    // main navigation - side menu
    items: [
        {
            label: 'Dashboard',
            permission: 'Dashboard',
            icon: 'ri-dashboard-line',
            link: route('dashboard.index')
        },
        {
            label: 'Contact Messages',
            permission: 'contact-message-list',
            icon: 'ri-mail-line',
            link: route('contactMessage.index')
        },
        {
            label: 'Order Management',
            permission: 'order-menu',
            children: [
                {
                    label: 'Pending Orders',
                    permission: 'order-list',
                    icon: 'ri-draft-line',
                    link: route('order.index')
                },
                {
                    label: 'Completed Orders',
                    permission: 'order-list',
                    icon: 'ri-draft-line',
                    link: route('order.index')
                }
            ]
        },

        {
            label: 'Product Management',
            permission: 'product-menu',
            children: [
                {
                    label: 'Products',
                    permission: 'product-list',
                    icon: 'ri-draft-line',
                    link: route('product.index')
                },
                {
                    label: 'Product Categories',
                    permission: 'product-category-list',
                    icon: 'ri-folders-line',
                    link: route('productCategory.index')
                },
                {
                    label: 'Product Tags',
                    permission: 'product-tag-list',
                    icon: 'ri-price-tag-3-line',
                    link: route('productTag.index')
                },
                {
                    label: 'Product Brands',
                    permission: 'product-brand-list',
                    icon: 'ri-team-line',
                    link: route('productBrand.index')
                }
            ]
        },

        {
            label: 'Customer Management',
            permission: 'customer-menu',
            children: [
                {
                    label: 'Customers',
                    permission: 'customer-list',
                    icon: 'ri-draft-line',
                    link: route('customer.index')
                }
            ]
        },

        // {
        //     label: 'Blog',
        //     permission: 'Blog',
        //     children: [
        //         {
        //             label: 'Posts',
        //             permission: 'Blog: Post - List',
        //             icon: 'ri-draft-line',
        //             link: route('blogPost.index')
        //         },
        //         {
        //             label: 'Categories',
        //             permission: 'Blog: Category - List',
        //             icon: 'ri-folders-line',
        //             link: route('blogCategory.index')
        //         },
        //         {
        //             label: 'Tags',
        //             permission: 'Blog: Tag - List',
        //             icon: 'ri-price-tag-3-line',
        //             link: route('blogTag.index')
        //         },
        //         {
        //             label: 'Authors',
        //             permission: 'Blog: Author - List',
        //             icon: 'ri-team-line',
        //             link: route('blogAuthor.index')
        //         }
        //     ]
        // },

        {
            label: 'Access Control List',
            permission: 'Acl',
            children: [
                {
                    label: 'Users',
                    permission: 'Acl: User - List',
                    icon: 'ri-user-line',
                    link: route('user.index')
                },
                {
                    label: 'Permissions',
                    permission: 'Acl: Permission - List',
                    icon: 'ri-shield-keyhole-line',
                    link: route('aclPermission.index')
                },
                {
                    label: 'Roles',
                    permission: 'Acl: Role - List',
                    icon: 'ri-account-box-line',
                    link: route('aclRole.index')
                }
            ]
        }
    ]
}
