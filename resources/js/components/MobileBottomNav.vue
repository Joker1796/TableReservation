<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CalendarCheck, CalendarDays, Newspaper, Settings2, User } from 'lucide-vue-next';
import { computed } from 'vue';
import type { Auth } from '@/types/auth';

const page = usePage<{ auth: Auth }>();
const isAdmin = computed(() => page.props.auth?.user?.is_admin === true);

function isActive(prefix: string, exact = false): boolean {
    return exact ? page.url === prefix : page.url.startsWith(prefix);
}
</script>

<template>
    <nav
        class="fixed bottom-0 left-0 right-0 z-50 border-t border-sidebar-border/70 bg-background md:hidden"
        style="padding-bottom: env(safe-area-inset-bottom, 0)"
    >
        <div class="flex h-16 items-stretch">
            <Link
                href="/feed"
                class="flex flex-1 flex-col items-center justify-center gap-1 text-[10px] font-medium transition-colors"
                :class="isActive('/feed', true) ? 'text-foreground' : 'text-muted-foreground'"
            >
                <Newspaper class="h-5 w-5" />
                <span>Лента</span>
            </Link>
            <Link
                href="/reservations"
                class="flex flex-1 flex-col items-center justify-center gap-1 text-[10px] font-medium transition-colors"
                :class="isActive('/reservations') ? 'text-foreground' : 'text-muted-foreground'"
            >
                <CalendarDays class="h-5 w-5" />
                <span>Бронирование</span>
            </Link>
            <Link
                href="/events"
                class="flex flex-1 flex-col items-center justify-center gap-1 text-[10px] font-medium transition-colors"
                :class="isActive('/events') ? 'text-foreground' : 'text-muted-foreground'"
            >
                <CalendarCheck class="h-5 w-5" />
                <span>События</span>
            </Link>
            <Link
                v-if="isAdmin"
                href="/admin/requests"
                class="flex flex-1 flex-col items-center justify-center gap-1 text-[10px] font-medium transition-colors"
                :class="isActive('/admin') ? 'text-foreground' : 'text-muted-foreground'"
            >
                <Settings2 class="h-5 w-5" />
                <span>Управление</span>
            </Link>
            <Link
                href="/settings/profile"
                class="flex flex-1 flex-col items-center justify-center gap-1 text-[10px] font-medium transition-colors"
                :class="isActive('/settings') ? 'text-foreground' : 'text-muted-foreground'"
            >
                <User class="h-5 w-5" />
                <span>Профиль</span>
            </Link>
        </div>
    </nav>
</template>
