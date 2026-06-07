<script setup lang="ts">
import { CalendarDate } from '@internationalized/date';
import type { DateValue } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import Calendar from '@/components/ui/calendar/Calendar.vue';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { cn } from '@/lib/utils';

const props = defineProps<{ modelValue: string }>();
const emit = defineEmits<{ 'update:modelValue': [value: string] }>();

const calendarDate = computed<CalendarDate | undefined>(() => {
    if (!props.modelValue) {
        return undefined;
    }

    const [datePart] = props.modelValue.split('T');
    const [y, m, d] = datePart.split('-').map(Number);

    if (!y || !m || !d) {
        return undefined;
    }

    return new CalendarDate(y, m, d);
});

const timeStr = computed(() => props.modelValue?.split('T')[1] ?? '00:00');
const timeHour = computed(() => timeStr.value.split(':')[0] ?? '00');
const timeMinute = computed(() => timeStr.value.split(':')[1] ?? '00');

const hours = Array.from({ length: 24 }, (_, i) => String(i).padStart(2, '0'));
const minutes = Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0'));

const triggerClass = computed(() => cn(
    'flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors',
    'hover:bg-accent/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
    props.modelValue ? 'text-foreground' : 'text-muted-foreground',
));

const formatted = computed(() => {
    const date = calendarDate.value;

    if (!date) {
        return '';
    }

    const d = new Date(date.year, date.month - 1, date.day);

    return `${d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' })}, ${timeStr.value}`;
});

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

function emitDateTime(date: CalendarDate, time: string): void {
    emit('update:modelValue', `${date.year}-${pad(date.month)}-${pad(date.day)}T${time}`);
}

function onDateSelect(date: DateValue | undefined): void {
    if (!date) {
        return;
    }

    emitDateTime(new CalendarDate(date.year, date.month, date.day), timeStr.value);
}

function onHourChange(h: string | number | bigint | Record<string, unknown> | null): void {
    const date = calendarDate.value;

    if (!date || !h) {
        return;
    }

    emitDateTime(date, `${String(h)}:${timeMinute.value}`);
}

function onMinuteChange(m: string | number | bigint | Record<string, unknown> | null): void {
    const date = calendarDate.value;

    if (!date || m === null) {
        return;
    }

    emitDateTime(date, `${timeHour.value}:${String(m)}`);
}
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <button type="button" :class="triggerClass">
                <span>{{ formatted || 'Выберите дату и время' }}</span>
                <CalendarIcon class="h-4 w-4 shrink-0 text-muted-foreground" />
            </button>
        </PopoverTrigger>
        <PopoverContent align="start">
            <Calendar :model-value="calendarDate" @update:model-value="onDateSelect" />
            <div class="flex items-center gap-3 border-t border-border px-3 pb-3 pt-3">
                <Label class="shrink-0 text-sm">Время</Label>
                <div class="flex items-center gap-1.5">
                    <Select :model-value="timeHour" @update:model-value="onHourChange">
                        <SelectTrigger class="w-[4.5rem]">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="h in hours" :key="h" :value="h">{{ h }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <span class="text-sm text-muted-foreground">:</span>
                    <Select :model-value="timeMinute" @update:model-value="onMinuteChange">
                        <SelectTrigger class="w-[4.5rem]">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="m in minutes" :key="m" :value="m">{{ m }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </PopoverContent>
    </Popover>
</template>
