<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Copy, Check } from 'lucide-vue-next';
import { ref } from 'vue';
import SecurityController, { editToken } from '@/actions/App/Http/Controllers/Settings/SecurityController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type Props = {
    token?: string | null;
};

withDefaults(defineProps<Props>(), {
    token: null,
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Токен',
                href: editToken(),
            },
        ],
    },
});

const copied = ref(false);

async function copyToken(token: string) {
    await navigator.clipboard.writeText(token);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
}
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

        <Form v-bind="SecurityController.generateToken.form()" class="space-y-4" #default="{ processing }">
            <Button type="submit" :disabled="processing" data-test="generate-token-button">
                Сгенерировать токен
            </Button>
        </Form>

        <div v-if="token" class="space-y-2" data-test="token-display">
            <p class="text-sm text-muted-foreground">Скопируйте токен сейчас — он больше не будет показан.</p>
            <div class="flex items-center gap-2">
                <Input :value="token" readonly class="font-mono text-sm" data-test="token-value" />
                <Button
                    type="button"
                    variant="outline"
                    size="icon"
                    :disabled="copied"
                    data-test="copy-token-button"
                    @click="copyToken(token!)"
                >
                    <Check v-if="copied" class="h-4 w-4" />
                    <Copy v-else class="h-4 w-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
