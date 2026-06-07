<script setup lang="ts">
import type { CalendarDate } from '@internationalized/date';
import type { CalendarRootEmits, CalendarRootProps } from 'reka-ui';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { useForwardPropsEmits } from 'reka-ui';
import {
    CalendarCell,
    CalendarCellTrigger,
    CalendarGrid,
    CalendarGridBody,
    CalendarGridHead,
    CalendarGridRow,
    CalendarHeadCell,
    CalendarHeader,
    CalendarHeading,
    CalendarNext,
    CalendarPrev,
    CalendarRoot,
} from 'reka-ui';
import { cn } from '@/lib/utils';

type Props = Omit<CalendarRootProps, 'modelValue'> & {
    modelValue?: CalendarDate;
};

const props = withDefaults(defineProps<Props>(), {
    locale: 'ru-RU',
    weekStartsOn: 1,
    weekdayFormat: 'short',
    fixedWeeks: true,
});

const emits = defineEmits<CalendarRootEmits>();
const forwarded = useForwardPropsEmits(props, emits);

const navBtnClass = cn(
    'inline-flex h-7 w-7 items-center justify-center rounded-md border border-input bg-background text-foreground',
    'hover:bg-accent hover:text-accent-foreground',
    'disabled:pointer-events-none disabled:opacity-50',
);

const cellClass = cn(
    'inline-flex h-9 w-9 items-center justify-center rounded-md text-sm font-normal transition-colors',
    'hover:bg-accent hover:text-accent-foreground',
    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
    'data-[selected]:bg-primary data-[selected]:text-primary-foreground data-[selected]:hover:bg-primary/90',
    'data-[today]:border data-[today]:border-primary',
    'data-[outside-month]:text-muted-foreground data-[outside-month]:opacity-40',
    'data-[disabled]:pointer-events-none data-[disabled]:opacity-30',
);
</script>

<template>
    <CalendarRoot v-bind="forwarded" class="p-3" v-slot="{ grid, weekDays }">
        <CalendarHeader class="mb-4 flex items-center justify-between">
            <CalendarPrev
                :class="navBtnClass"
            >
                <ChevronLeft class="h-4 w-4" />
            </CalendarPrev>
            <CalendarHeading class="text-sm font-medium capitalize" />
            <CalendarNext
                :class="navBtnClass"
            >
                <ChevronRight class="h-4 w-4" />
            </CalendarNext>
        </CalendarHeader>

        <div v-for="month in grid" :key="month.value.toString()">
            <CalendarGrid class="w-full border-collapse">
                <CalendarGridHead>
                    <CalendarGridRow class="flex">
                        <CalendarHeadCell
                            v-for="day in weekDays"
                            :key="day"
                            class="w-9 text-center text-xs font-medium text-muted-foreground"
                        >
                            {{ day }}
                        </CalendarHeadCell>
                    </CalendarGridRow>
                </CalendarGridHead>
                <CalendarGridBody>
                    <CalendarGridRow
                        v-for="(week, i) in month.rows"
                        :key="i"
                        class="mt-1 flex"
                    >
                        <CalendarCell
                            v-for="day in week"
                            :key="day.toString()"
                            :date="day"
                            class="relative p-0 text-center"
                        >
                            <CalendarCellTrigger
                                :day="day"
                                :month="month.value"
                                :class="cellClass"
                            />
                        </CalendarCell>
                    </CalendarGridRow>
                </CalendarGridBody>
            </CalendarGrid>
        </div>
    </CalendarRoot>
</template>
