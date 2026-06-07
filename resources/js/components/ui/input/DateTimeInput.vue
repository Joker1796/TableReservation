<script setup lang="ts">
import { onMounted } from 'vue';
import { Input } from '@/components/ui/input';

defineOptions({ inheritAttrs: false });

const model = defineModel<string>();

onMounted(() => {
    if (!model.value) {
        const d = new Date();
        model.value = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}T${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
    }
});

function openPicker(e: MouseEvent): void {
    try {
        (e.currentTarget as HTMLInputElement).showPicker();
    } catch {}
}
</script>

<template>
    <Input v-model="model" type="datetime-local" v-bind="$attrs" @click="openPicker" />
</template>
