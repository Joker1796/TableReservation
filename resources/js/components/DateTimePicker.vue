<script setup lang="ts">
import { CalendarDate, type DateValue } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import Calendar from '@/components/ui/calendar/Calendar.vue';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';

const props = defineProps<{ modelValue: string }>();
const emit = defineEmits<{ 'update:modelValue': [value: string] }>();

const calendarDate = computed<CalendarDate | undefined>(() => {
    if (!props.modelValue) return undefined;
    const [datePart] = props.modelValue.split('T');
    const [y, m, d] = datePart.split('-').map(Number);
    if (!y || !m || !d) return undefined;
    return new CalendarDate(y, m, d);
});

const timeStr = computed(() => props.modelValue?.split('T')[1] ?? '00:00');

const formatted = computed(() => {
    const date = calendarDate.value;
    if (!date) return '';
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
    if (!date) return;
    emitDateTime(new CalendarDate(date.year, date.month, date.day), timeStr.value);
}

function onTimeChange(e: Event): void {
    const date = calendarDate.value;
    if (!date) return;
    emitDateTime(date, (e.target as HTMLInputElement).value);
}
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <button
                type="button"
                :class="
                    cn(
                        'flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs transition-colors',
                        'hover:bg-accent/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
                        modelValue ? 'text-foreground' : 'text-muted-foreground',
                    )
                "
            >
                <span>{{ formatted || 'Выберите дату и время' }}</span>
                <CalendarIcon class="h-4 w-4 shrink-0 text-muted-foreground" />
            </button>
        </PopoverTrigger>
        <PopoverContent align="start">
            <Calendar :model-value="calendarDate" @update:model-value="onDateSelect" />
            <div class="flex items-center gap-3 border-t border-border px-3 pb-3 pt-3">
                <Label class="shrink-0 text-sm">Время</Label>
                <input
                    type="time"
                    :value="timeStr"
                    :class="
                        cn(
                            'h-8 w-full rounded-md border border-input bg-background px-2 text-sm',
                            'focus:outline-none focus:ring-2 focus:ring-ring',
                        )
                    "
                    @change="onTimeChange"
                />
            </div>
        </PopoverContent>
    </Popover>
</template>
