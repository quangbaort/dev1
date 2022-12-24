let apps = [{
        path: '/notify',
        name: 'apps-notify',
        component: () =>
            import ('@/views/apps/notify/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.notify'),
        },
        role : [1,5],
        function : 'Notify',
        type : 'link'
    },
    {
        path: '/safety',
        name: 'apps-safety',
        component: () =>
            import ('@/views/apps/safety/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.safe_confirm'),
        },
        role : [1,5],
        function : 'Safety',
        type : 'link'
    },
    {
        path: '/folder',
        name: 'apps-folder',
        component: () =>
            import ('@/views/apps/folder/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.archive'),
        },
        role : [1,5],
        function : 'Folder',
        type : 'link'
    },
    {
        path: '/calendar',
        name: 'apps-calendar',
        component: () =>
            import ('@/views/apps/calendar/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.event'),

        },
        role : [1,5],
        function : 'Calendar',
        type : 'link'
    },
    {
        path: '/users',
        name: 'apps-user-list',
        component: () =>
            import ('@/views/apps/users/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.user'),

        },
        role : [1],
        function : 'User',
        type : 'link'
    },
    {
        path: '/organizations',
        name: 'apps-organizations',
        component: () =>
            import ('@/views/apps/organizations/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.organization'),
        },
        role : [1],
        function : 'Organization',
        type : 'link'
    },
    {
        path: '/company',
        name: 'apps-company-list',
        component: () =>
            import ('@/views/apps/companies/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.company'),
        },
        role : [1],
        function : 'Company',
        type : 'link'
    },
    {
        path: '/board',
        name: 'apps-board-list',
        component: () =>
            import ('@/views/apps/board/list/List.vue'),
        meta: {
            layout: 'content',
            title: trans('app.branch_committee'),
        },
        role : [1],
        function : 'Board',
        type : 'link'
    },
    {
        path: '/notifyGroup',
        name: 'apps-notifyGroup',
        component: () =>
            import ('@/views/apps/notifyGroup/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.notify_destination'),
        },
        role : [1],
        function : 'MailingList',
        type : 'link'
    },
    {
        path: '/holiday',
        name: 'apps-holiday',
        component: () =>
            import ('@/views/apps/holidays/index.vue'),
        meta: {
            layout: 'content',
            title: trans('app.holiday'),
        },
        role : [1],
        function : 'Holiday',
        type : 'link'
    },
    {
        path: '/memo',
        name: 'apps-memo-list',
        component: () =>
            import ('@/views/apps/memo/list/List.vue'),
        meta: {
            layout: 'content',
            title: trans('app.memo'),
        },
        role : [1],
        function : 'Memo',
        type : 'link'
    },
    {
      path: '/logs',
      name: 'apps-log-list',
      component: () =>
          import ('@/views/apps/log/list/List.vue'),
      meta: {
          layout: 'content',
          title: trans('app.log'),
      },
      role : [1],
      function : 'Logs',
      type : 'link'
    }

]


export default apps
