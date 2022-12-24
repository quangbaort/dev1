const pages = [
  {
    path: '/ui/typography',
    name: 'ui-typography',
    component: () => import('@/views/pages/typography/Typography.vue'),
    meta: {
      layout: 'content',
    },
  },
]

export default pages
