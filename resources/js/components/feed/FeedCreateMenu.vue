<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ChevronDown, Settings } from 'lucide-vue-next';
import { ref } from 'vue';
import CreateEventModal from '@/components/feed/CreateEventModal.vue';
import CreatePollModal from '@/components/feed/CreatePollModal.vue';
import CreatePostModal from '@/components/feed/CreatePostModal.vue';
import SuggestEventModal from '@/components/feed/SuggestEventModal.vue';
import SuggestPostModal from '@/components/feed/SuggestPostModal.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { useHasContacts } from '@/composables/useHasContacts';
import type { Auth } from '@/types/auth';

const page = usePage<{ auth: Auth }>();
const canCreate = page.props.auth.user.is_admin || page.props.auth.user.is_editor;

const postOpen = ref(false);
const pollOpen = ref(false);
const eventOpen = ref(false);
const hasContacts = useHasContacts();
const suggestOpen = ref(false);
const suggestEventOpen = ref(false);
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
                    <DropdownMenuItem @select.prevent="eventOpen = true">Создать событие</DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <CreatePostModal :open="postOpen" @update:open="postOpen = $event" />
            <CreatePollModal :open="pollOpen" @update:open="pollOpen = $event" />
            <CreateEventModal :open="eventOpen" @update:open="eventOpen = $event" />
        </template>

        <template v-else>
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger as-child>
                        <span>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child :disabled="!hasContacts">
                                    <Button variant="outline" size="sm" :disabled="!hasContacts">
                                        Предложить <ChevronDown class="ml-1 h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem @select.prevent="suggestOpen = true">Новость</DropdownMenuItem>
                                    <DropdownMenuItem @select.prevent="suggestEventOpen = true">Событие</DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </span>
                    </TooltipTrigger>
                    <TooltipContent v-if="!hasContacts">Заполните контактные данные в профиле</TooltipContent>
                </Tooltip>
            </TooltipProvider>
            <SuggestPostModal :open="suggestOpen" @update:open="suggestOpen = $event" />
            <SuggestEventModal :open="suggestEventOpen" @update:open="suggestEventOpen = $event" />
        </template>
    </div>
</template>
