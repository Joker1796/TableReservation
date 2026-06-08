import admin from '@/routes/admin'
import type { BreadcrumbItem } from '@/types'

const adminRoot: BreadcrumbItem = { title: 'Админ', href: admin.requests.index.url() }
const eventsRoot: BreadcrumbItem = { title: 'События', href: admin.events.index.url() }
const postsRoot: BreadcrumbItem = { title: 'Публикации', href: admin.posts.index.url() }
const tablesRoot: BreadcrumbItem = { title: 'Столы', href: admin.tables.index.url() }
const reservationsRoot: BreadcrumbItem = { title: 'Резервирования', href: admin.reservations.index.url() }
const requestsRoot: BreadcrumbItem = { title: 'Заявки', href: admin.requests.index.url() }
const workshopRoot: BreadcrumbItem = { title: 'Мастерская', href: admin.workshop.index.url() }

export const adminBreadcrumbs = {
    events: {
        index: [adminRoot, eventsRoot],
        create: [adminRoot, eventsRoot, { title: 'Создать', href: admin.events.create.url() }],
        edit: [adminRoot, eventsRoot, { title: 'Редактировать', href: '#' }],
    },
    posts: {
        index: [adminRoot, postsRoot],
        create: [adminRoot, postsRoot, { title: 'Создать', href: admin.posts.create.url() }],
        edit: [adminRoot, postsRoot, { title: 'Редактировать', href: '#' }],
    },
    tables: {
        index: [adminRoot, tablesRoot],
        create: [adminRoot, tablesRoot, { title: 'Создать', href: admin.tables.create.url() }],
        edit: [adminRoot, tablesRoot, { title: 'Редактировать', href: '#' }],
    },
    reservations: {
        index: [adminRoot, reservationsRoot],
    },
    requests: {
        index: [adminRoot, requestsRoot],
    },
    workshop: {
        index: [adminRoot, workshopRoot],
    },
}
