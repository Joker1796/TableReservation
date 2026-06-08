<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ParticipantBadge from '@/components/ParticipantBadge.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import type { Event } from '@/types/event';

const props = defineProps<{ event: Event }>();

const page = usePage<{ auth: { user: { id: number } | null } }>();
const open = ref(false);

const isRegistered = computed(() =>
    props.event.participants.some((p) => p.id === page.props.auth.user?.id),
);
const isPast = computed(() => new Date(props.event.starts_at) < new Date());

function formatDate(iso: string): string {
    return new Date(iso).toLocaleString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function toggleRegistration(): void {
    if (isRegistered.value) {
        router.delete(`/events/${props.event.id}/register`, { preserveScroll: true });
    } else {
        router.post(`/events/${props.event.id}/register`, {}, { preserveScroll: true });
    }
}
</script>

<template>
    <button type="button" class="block w-full rounded-lg border border-border bg-card p-4 text-left transition-colors hover:bg-accent" @click="open = true">
        <p class="text-sm font-medium leading-snug">{{ event.title }}</p>
        <p class="mt-1 text-xs text-muted-foreground">
            {{ new Date(event.starts_at).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }) }}
            <template v-if="event.ends_at"> — {{ new Date(event.ends_at).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }) }}</template>
        </p>
        <p v-if="event.short_description" class="mt-1.5 line-clamp-4 text-xs text-muted-foreground">{{ event.short_description }}</p>
    </button>

    <Dialog :open="open" @update:open="open = $event">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ event.title }}</DialogTitle>
            </DialogHeader>

            <div class="space-y-4 text-sm">
                <div class="flex flex-col gap-1 text-muted-foreground">
                    <span>Начало: {{ formatDate(event.starts_at) }}</span>
                    <span v-if="event.ends_at">Конец: {{ formatDate(event.ends_at) }}</span>
                </div>

                <p v-if="event.description" class="whitespace-pre-wrap leading-relaxed">{{ event.description }}</p>
                <p v-else-if="event.short_description" class="leading-relaxed">{{ event.short_description }}</p>
                <p v-else class="text-muted-foreground">Описание не указано</p>

                <div>
                    <p class="mb-1.5 text-xs font-medium uppercase tracking-wide text-muted-foreground">Участники</p>
                    <div v-if="event.participants.length > 0" class="flex flex-wrap gap-1">
                        <ParticipantBadge v-for="p in event.participants" :key="p.id" :participant="p" />
                    </div>
                    <p v-else class="text-muted-foreground">Участников пока нет</p>
                </div>
            </div>

            <DialogFooter v-if="page.props.auth.user">
                <p v-if="isPast" class="text-sm text-muted-foreground">Событие уже прошло</p>
                <Button
                    v-else
                    :variant="isRegistered ? 'outline' : 'default'"
                    @click="toggleRegistration(); open = false"
                >
                    {{ isRegistered ? 'Отписаться' : 'Записаться' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
