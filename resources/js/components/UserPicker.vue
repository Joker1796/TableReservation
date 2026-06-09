<script setup lang="ts">
import { onClickOutside } from '@vueuse/core';
import { Check, ChevronDown, Users, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import type { ReservationUser } from '@/types/reservation';

const props = defineProps<{
    users: ReservationUser[];
    modelValue: number[];
}>();

const emit = defineEmits<{
    'update:modelValue': [value: number[]];
}>();

const open = ref(false);
const pickerRef = ref<HTMLElement | null>(null);

onClickOutside(pickerRef, () => {
    open.value = false;
});

function toggleUser(id: number): void {
    const next = props.modelValue.includes(id)
        ? props.modelValue.filter((uid) => uid !== id)
        : [...props.modelValue, id];

    emit('update:modelValue', next);
}
</script>

<template>
    <div ref="pickerRef" class="relative">
        <button type="button" class="picker-trigger" @click="open = !open">
            <span class="flex items-center gap-2 text-muted-foreground">
                <Users class="h-4 w-4 shrink-0" />
                {{ modelValue.length === 0 ? 'Добавить участников' : `Выбрано: ${modelValue.length}` }}
            </span>
            <ChevronDown class="h-4 w-4 shrink-0 text-muted-foreground" :class="{ 'rotate-180': open }" />
        </button>

        <div
            v-if="open"
            class="absolute z-50 mt-1 max-h-52 w-full overflow-y-auto rounded-md border bg-popover shadow-md"
        >
            <button
                v-for="user in users"
                :key="user.id"
                type="button"
                class="flex w-full items-center gap-2 px-3 py-2 text-sm hover:bg-accent"
                @click="toggleUser(user.id)"
            >
                <Check
                    class="h-4 w-4 shrink-0"
                    :class="modelValue.includes(user.id) ? 'text-primary' : 'text-transparent'"
                />
                <span class="font-medium">{{ user.name }}</span>
                <span class="ml-auto text-xs text-muted-foreground">{{ user.email }}</span>
            </button>
        </div>
    </div>

    <div v-if="modelValue.length > 0" class="flex flex-wrap gap-1.5">
        <span v-for="user in users.filter((u) => modelValue.includes(u.id))" :key="user.id" class="user-tag">
            <Avatar class="h-5 w-5">
                <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                <AvatarFallback class="text-[10px] font-semibold bg-primary text-primary-foreground">{{ user.name.charAt(0).toUpperCase() }}</AvatarFallback>
            </Avatar>
            <span class="font-medium">{{ user.name }}</span>
            <button
                type="button"
                class="ml-0.5 rounded-full text-muted-foreground hover:text-foreground"
                @click="toggleUser(user.id)"
            >
                <X class="h-3 w-3" />
            </button>
        </span>
    </div>
</template>
