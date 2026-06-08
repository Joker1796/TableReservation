import { home } from '@/routes'
import reservations from '@/routes/reservations'
import type { BreadcrumbItem } from '@/types'

const homeRoot: BreadcrumbItem = { title: 'Главная', href: home.url() }
const reservationsRoot: BreadcrumbItem = { title: 'Резервирования', href: '/reservations' }

export const reservationBreadcrumbs = {
    index: [homeRoot, reservationsRoot],
    create: [reservationsRoot, { title: 'Создать', href: reservations.create.url() }],
    show: [reservationsRoot, { title: 'Подробнее', href: '#' }],
    edit: [reservationsRoot, { title: 'Редактировать', href: '#' }],
}
