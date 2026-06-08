import { home } from '@/routes'
import type { BreadcrumbItem } from '@/types'

const homeRoot: BreadcrumbItem = { title: 'Главная', href: home.url() }

export const appBreadcrumbs = {
    events: [homeRoot, { title: 'События', href: '/events' }],
    feed: [homeRoot, { title: 'Лента', href: '/feed' }],
}
