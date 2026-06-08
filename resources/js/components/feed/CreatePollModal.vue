<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';
import DateTimePicker from '@/components/DateTimePicker.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const MAX_OPTIONS = 20;

withDefaults(defineProps<{ open?: boolean }>(), { open: undefined });
const emit = defineEmits<{ 'update:open': [value: boolean] }>();

const form = useForm({
    question: '',
    description: '',
    allow_multiple: false,
    closes_at: '',
    options: ['', ''] as string[],
});

function addOption(): void {
    if (form.options.length < MAX_OPTIONS) {
        form.options = [...form.options, ''];
    }
}

function removeOption(index: number): void {
    if (form.options.length > 2) {
        form.options = form.options.filter((_, i) => i !== index);
    }
}

function submit(): void {
    form.post('/feed/polls', {
        onSuccess: () => {
            emit('update:open', false);
            form.reset();
            form.options = ['', ''];
        },
    });
}

function onOpenChange(value: boolean): void {
    emit('update:open', value);

    if (!value) {
        form.reset();
        form.options = ['', ''];
        form.clearErrors();
    }
}
</script>

<template>
    <Dialog :open="open" @update:open="onOpenChange">
        <DialogTrigger v-if="$slots.default" as-child>
            <slot />
        </DialogTrigger>
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Новый опрос</DialogTitle>
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="poll-question">Вопрос <span class="text-destructive">*</span></Label>
                    <Input id="poll-question" v-model="form.question" placeholder="Формулировка вопроса" required maxlength="255" />
                    <InputError :message="form.errors.question" />
                </div>

                <div class="grid gap-2">
                    <Label for="poll-description">Описание</Label>
                    <Input id="poll-description" v-model="form.description" placeholder="Краткое описание (необязательно)" maxlength="1000" />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label>Варианты ответов <span class="text-destructive">*</span></Label>
                    <div class="space-y-2">
                        <div v-for="(_, index) in form.options" :key="index" class="flex items-center gap-2">
                            <Input
                                v-model="form.options[index]"
                                :placeholder="`Вариант ${index + 1}`"
                                required
                                maxlength="255"
                                class="flex-1"
                            />
                            <Button
                                v-if="form.options.length > 2"
                                type="button"
                                variant="ghost"
                                size="icon"
                                class="shrink-0 text-muted-foreground hover:text-destructive"
                                @click="removeOption(index)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                    <InputError :message="form.errors.options" />
                    <Button
                        v-if="form.options.length < MAX_OPTIONS"
                        type="button"
                        variant="outline"
                        size="sm"
                        class="mt-1 w-fit"
                        @click="addOption"
                    >
                        <Plus class="mr-1 h-3 w-3" />
                        Добавить вариант
                    </Button>
                    <p class="text-xs text-muted-foreground">{{ form.options.length }}/{{ MAX_OPTIONS }} вариантов</p>
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox
                        id="poll-multiple"
                        :model-value="form.allow_multiple"
                        @update:model-value="(v) => (form.allow_multiple = !!v)"
                    />
                    <Label for="poll-multiple" class="cursor-pointer font-normal">Разрешить выбор нескольких вариантов</Label>
                </div>

                <div class="grid gap-2">
                    <Label>Дата закрытия</Label>
                    <DateTimePicker v-model="form.closes_at" />
                    <InputError :message="form.errors.closes_at" />
                    <p class="text-xs text-muted-foreground">Оставьте пустым, чтобы опрос не закрывался</p>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onOpenChange(false)">Отмена</Button>
                    <Button type="submit" :disabled="form.processing">Создать</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
