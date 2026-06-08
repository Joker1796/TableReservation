<script setup lang="ts">
import DateTimePicker from '@/components/DateTimePicker.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

defineProps<{
    title: string;
    shortDescription: string;
    description: string;
    startsAt: string;
    endsAt: string;
    errors: Partial<Record<'title' | 'short_description' | 'description' | 'starts_at' | 'ends_at', string>>;
}>();

defineEmits<{
    'update:title': [string];
    'update:shortDescription': [string];
    'update:description': [string];
    'update:startsAt': [string];
    'update:endsAt': [string];
}>();
</script>

<template>
    <div class="grid gap-2">
        <Label for="event-title">Название <span class="text-destructive">*</span></Label>
        <Input
            id="event-title"
            :model-value="title"
            placeholder="Название события"
            required
            maxlength="255"
            @update:model-value="$emit('update:title', $event as string)"
        />
        <InputError :message="errors.title" />
    </div>

    <div class="grid gap-2">
        <Label for="event-short-desc">Краткое описание <span class="text-muted-foreground text-xs">(для ленты)</span></Label>
        <Textarea
            id="event-short-desc"
            :model-value="shortDescription"
            placeholder="Пара предложений для анонса"
            rows="2"
            maxlength="150"
            @update:model-value="$emit('update:shortDescription', $event as string)"
        />
        <div class="flex justify-end text-xs text-muted-foreground">{{ shortDescription.length }}/150</div>
        <InputError :message="errors.short_description" />
    </div>

    <div class="grid gap-2">
        <Label for="event-desc">Описание</Label>
        <Textarea
            id="event-desc"
            :model-value="description"
            placeholder="Подробное описание события"
            rows="4"
            @update:model-value="$emit('update:description', $event as string)"
        />
        <InputError :message="errors.description" />
    </div>

    <div class="grid gap-2">
        <Label>Начало <span class="text-destructive">*</span></Label>
        <DateTimePicker
            :model-value="startsAt"
            @update:model-value="$emit('update:startsAt', $event)"
        />
        <InputError :message="errors.starts_at" />
    </div>

    <div class="grid gap-2">
        <Label>Конец</Label>
        <DateTimePicker
            :model-value="endsAt"
            @update:model-value="$emit('update:endsAt', $event)"
        />
        <InputError :message="errors.ends_at" />
    </div>
</template>
