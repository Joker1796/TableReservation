<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Settings } from 'lucide-vue-next';
import { ref } from 'vue';
import CreatePollModal from '@/components/feed/CreatePollModal.vue';
import CreatePostModal from '@/components/feed/CreatePostModal.vue';
import SuggestPostModal from '@/components/feed/SuggestPostModal.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { Auth } from '@/types/auth';

const page = usePage<{ auth: Auth }>();
const canCreate = page.props.auth.user.is_admin || page.props.auth.user.is_editor;

const postOpen = ref(false);
const pollOpen = ref(false);
const suggestOpen = ref(false);
</script>

<template>
    <div class="flex items-center">
        <template v-if="canCreate">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" aria-label="Создать контент">
                        <Settings class="h-5 w-5" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuItem @select.prevent="postOpen = true">Создать новость</DropdownMenuItem>
                    <DropdownMenuItem @select.prevent="pollOpen = true">Создать опрос</DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <CreatePostModal :open="postOpen" @update:open="postOpen = $event" />
            <CreatePollModal :open="pollOpen" @update:open="pollOpen = $event" />
        </template>

        <template v-else>
            <Button variant="outline" size="sm" @click="suggestOpen = true">Предложить новость</Button>
            <SuggestPostModal :open="suggestOpen" @update:open="suggestOpen = $event" />
        </template>
    </div>
</template>
