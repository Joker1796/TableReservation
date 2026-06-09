<script setup lang="ts">
import UserPopover from '@/components/UserPopover.vue';

defineProps<{
    participant: { id: number; name?: string; email?: string | null; avatar?: string | null; phone?: string | null; contacts?: string | null };
}>();
</script>

<template>
    <UserPopover :user="participant">
        <button class="inline-block cursor-pointer rounded bg-muted px-1.5 py-0.5 text-xs text-muted-foreground hover:bg-muted/70">
            {{ participant.name }}
        </button>
    </UserPopover>
</template>
