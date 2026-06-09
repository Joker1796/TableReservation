<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { getInitials } from '@/composables/useInitials';

const props = defineProps<{
    user: {
        id: number;
        name?: string;
        email?: string | null;
        avatar?: string | null;
        phone?: string | null;
        contacts?: string | null;
    };
    isCurrentUser?: boolean;
}>();

const fallbackClass = computed(() =>
    props.isCurrentUser ? 'bg-primary text-primary-foreground' : 'bg-muted-foreground/20 text-foreground'
);
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <slot />
        </PopoverTrigger>
        <PopoverContent class="w-56 p-3 text-sm">
            <div class="mb-2 flex items-center gap-2.5">
                <Avatar class="h-8 w-8">
                    <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                    <AvatarFallback class="text-xs font-semibold" :class="fallbackClass">
                        {{ getInitials(user.name) }}
                    </AvatarFallback>
                </Avatar>
                <div class="min-w-0">
                    <p class="truncate font-medium">{{ user.name }}</p>
                    <p v-if="user.email" class="truncate text-xs text-muted-foreground">{{ user.email }}</p>
                </div>
            </div>
            <div v-if="user.phone || user.contacts" class="space-y-1 border-t pt-2">
                <p v-if="user.phone" class="text-muted-foreground">{{ user.phone }}</p>
                <p v-if="user.contacts" class="text-muted-foreground">{{ user.contacts }}</p>
            </div>
            <p v-else class="border-t pt-2 text-muted-foreground">Контакты не указаны</p>
        </PopoverContent>
    </Popover>
</template>
