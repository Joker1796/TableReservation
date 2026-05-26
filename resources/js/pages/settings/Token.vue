<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Токен',
                // href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Создание API токена" />

    <h1 class="sr-only">Создание API токена</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Создание API токена"
            description="Здесь вы можете сгенерировать api-токен для вашего аккаунта"
        />
    </div>
</template>
