<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { edit } from '@/routes/appearance';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Настройки внешнего вида интерфейса',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Внешний вид" />

    <h1 class="sr-only">Настройки внешнего вида интерфейса</h1>

    <div class="space-y-6">
        <Heading variant="small" title="Выбор темы" />
        <AppearanceTabs />
    </div>
</template>
