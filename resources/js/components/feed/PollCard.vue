<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import type { PollFeedItem, PollOption } from '@/types/feed';

const props = defineProps<{ item: PollFeedItem }>();

const pollState = ref<PollFeedItem>({ ...props.item, options: [...props.item.options] });
const selectedIds = ref<number[]>([]);
const submitting = ref(false);
const error = ref<string | null>(null);

function formatDate(iso: string): string {
    return new Date(iso).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function toggleOption(optionId: number): void {
    if (!pollState.value.allow_multiple) {
        selectedIds.value = [optionId];

        return;
    }

    const idx = selectedIds.value.indexOf(optionId);

    if (idx === -1) {
        selectedIds.value = [...selectedIds.value, optionId];
    } else {
        selectedIds.value = selectedIds.value.filter((id) => id !== optionId);
    }
}

function isSelected(optionId: number): boolean {
    return selectedIds.value.includes(optionId);
}

function votePercent(option: PollOption): number {
    if (pollState.value.total_votes === 0) {
        return 0;
    }

    return Math.round((option.votes_count / pollState.value.total_votes) * 100);
}

async function submitVote(): Promise<void> {
    if (selectedIds.value.length === 0) {
        return;
    }

    submitting.value = true;
    error.value = null;

    try {
        const csrfToken =
            (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content ?? '';
        const response = await fetch(`/feed/polls/${pollState.value.id}/vote`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify({ option_ids: selectedIds.value }),
        });

        if (!response.ok) {
            const data = await response.json();
            error.value = data.message ?? 'Ошибка голосования.';

            return;
        }

        const updated = await response.json();
        pollState.value = updated;
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <article class="border border-border bg-card p-4">
        <div class="mb-3 flex items-center gap-2 text-sm text-muted-foreground">
            <span v-if="pollState.author" class="font-medium text-foreground">{{ pollState.author.name }}</span>
            <span>·</span>
            <time :datetime="pollState.published_at">{{ formatDate(pollState.published_at) }}</time>
            <span v-if="!pollState.is_open" class="ml-auto rounded bg-muted px-1.5 py-0.5 text-xs">Завершён</span>
        </div>

        <h2 class="mb-1 text-lg font-semibold leading-snug">{{ pollState.question }}</h2>
        <p v-if="pollState.description" class="mb-3 text-sm text-muted-foreground">{{ pollState.description }}</p>

        <!-- Results view -->
        <div v-if="pollState.has_voted" class="space-y-2">
            <div v-for="option in pollState.options" :key="option.id" class="space-y-1">
                <div class="flex items-center justify-between text-sm">
                    <span :class="pollState.user_vote_ids.includes(option.id) ? 'font-semibold text-foreground' : 'text-muted-foreground'">
                        {{ option.text }}
                    </span>
                    <span class="text-muted-foreground">{{ votePercent(option) }}%</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                    <div
                        class="h-2 rounded-full bg-primary transition-all"
                        :style="{ width: `${votePercent(option)}%` }"
                    />
                </div>
            </div>
            <p class="mt-2 text-xs text-muted-foreground">Всего голосов: {{ pollState.total_votes }}</p>
        </div>

        <!-- Voting view -->
        <div v-else-if="pollState.is_open" class="space-y-2">
            <div
                v-for="option in pollState.options"
                :key="option.id"
                class="flex cursor-pointer items-center gap-3 rounded border border-border px-3 py-2 text-sm hover:bg-muted/50"
                :class="{ 'border-primary bg-primary/5': isSelected(option.id) }"
                @click="toggleOption(option.id)"
            >
                <Checkbox
                    :model-value="isSelected(option.id)"
                    :class="!pollState.allow_multiple ? 'rounded-full' : ''"
                    @update:model-value="toggleOption(option.id)"
                    @click.stop
                />
                <span>{{ option.text }}</span>
            </div>

            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>

            <Button
                size="sm"
                :disabled="selectedIds.length === 0 || submitting"
                class="mt-1"
                @click="submitVote"
            >
                {{ submitting ? 'Отправка...' : 'Проголосовать' }}
            </Button>
            <p v-if="pollState.allow_multiple" class="text-xs text-muted-foreground">Можно выбрать несколько вариантов</p>
        </div>

        <!-- Closed, not voted -->
        <div v-else class="space-y-1 text-sm text-muted-foreground">
            <p v-for="option in pollState.options" :key="option.id">{{ option.text }}</p>
        </div>
    </article>
</template>
