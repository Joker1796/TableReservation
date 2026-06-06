<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';

const props = defineProps<{ status: 0 | 1 | 2 }>();

const variant = computed(() => {
    if (props.status === 1) {
return 'default';
}

    if (props.status === 2) {
return 'destructive';
}

    return 'outline';
});

const label = computed(() => {
    if (props.status === 1) {
return 'Одобрена';
}

    if (props.status === 2) {
return 'Отклонена';
}

    return 'На рассмотрении';
});
</script>

<template>
    <Badge :variant="variant" class="text-xs">{{ label }}</Badge>
</template>
