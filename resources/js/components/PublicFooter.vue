<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <footer class="border-t border-[#dddaf0] px-6 py-8 dark:border-[#352f5a]">
        <div class="mx-auto flex max-w-5xl items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="flex size-7 items-center justify-center rounded-md bg-[#453c77] dark:bg-[#e8e6f0]">
                    <AppLogoIcon class="size-4 text-white dark:text-[#453c77]" />
                </div>
                <span class="text-sm font-medium">Кочующий стол</span>
            </div>
            <p class="text-sm text-[#6b6585] dark:text-[#9b98af]">© 2025 Все права защищены</p>
        </div>
    </footer>
</template>
