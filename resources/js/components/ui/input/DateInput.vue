<script setup lang="ts">
import { onMounted } from 'vue';
import { Input } from '@/components/ui/input';

defineOptions({ inheritAttrs: false });

const model = defineModel<string>();

onMounted(() => {
    if (!model.value) {
        const d = new Date();
        d.setDate(d.getDate() + 1);
        model.value = d.toISOString().substring(0, 10);
    }
});

function openPicker(e: MouseEvent): void {
    try {
        (e.currentTarget as HTMLInputElement).showPicker();
    } catch {}
}
</script>

<template>
    <Input v-model="model" type="date" v-bind="$attrs" @click="openPicker" />
</template>
