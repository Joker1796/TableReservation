import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Auth } from '@/types/auth';

export function useHasContacts() {
    const page = usePage<{ auth: Auth }>();

    return computed(() => !!(page.props.auth.user.phone || page.props.auth.user.contacts));
}
