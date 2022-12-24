const dashboard = [
  {
    path: '/dashboards/',
    name: 'dashboard',
    component: () => import('@/views/dashboards/index.vue'),
    meta: {
      layout: 'content',
    },
  },
]

export default dashboard
