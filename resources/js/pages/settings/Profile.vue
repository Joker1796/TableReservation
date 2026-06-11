<script setup lang="ts">
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import AvatarCropModal from '@/components/AvatarCropModal.vue';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useInitials } from '@/composables/useInitials';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Настройки профиля',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const { getInitials } = useInitials();
const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);
const selectedFile = ref<File | Blob | null>(null);
const cropSrc = ref<string | null>(null);
const cropOpen = ref(false);

function onFileChange(e: Event): void {
    const file = (e.target as HTMLInputElement).files?.[0];

    if (!file) {
return;
}

    const reader = new FileReader();
    reader.onload = () => {
        cropSrc.value = reader.result as string;
        cropOpen.value = true;
    };
    reader.readAsDataURL(file);
}

function onCropConfirm(blob: Blob): void {
    selectedFile.value = blob;
    previewUrl.value = URL.createObjectURL(blob);
    cropOpen.value = false;
}

function onCropCancel(): void {
    cropOpen.value = false;

    if (fileInput.value) {
fileInput.value.value = '';
}
}

function saveAvatar(): void {
    if (!selectedFile.value) {
return;
}

    router.post('/settings/avatar', { avatar: selectedFile.value }, {
        onSuccess: () => {
            previewUrl.value = null;
            selectedFile.value = null;

            if (fileInput.value) {
fileInput.value.value = '';
}
        },
    });
}

function cancelAvatar(): void {
    previewUrl.value = null;
    selectedFile.value = null;

    if (fileInput.value) {
fileInput.value.value = '';
}
}

function deleteAvatar(): void {
    router.delete('/settings/avatar');
}
</script>

<template>
    <Head title="Настройки профиля" />

    <h1 class="sr-only">Настройки профиля</h1>

    <div class="flex flex-col space-y-6">
        <Heading variant="small" title="Фото профиля" />

        <div class="flex items-center gap-4">
            <Avatar class="h-16 w-16">
                <AvatarImage v-if="previewUrl ?? user.avatar" :src="(previewUrl ?? user.avatar)!" :alt="user.name" />
                <AvatarFallback class="text-lg font-semibold">{{ getInitials(user.name) }}</AvatarFallback>
            </Avatar>
            <div class="flex flex-col gap-1.5">
                <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileChange" />
                <template v-if="!selectedFile">
                    <Button type="button" variant="outline" size="sm" @click="fileInput?.click()">Сменить фото</Button>
                    <Button v-if="user.avatar" type="button" variant="ghost" size="sm" class="text-muted-foreground" @click="deleteAvatar">Удалить фото</Button>
                </template>
                <template v-else>
                    <Button type="button" size="sm" @click="saveAvatar">Сохранить</Button>
                    <Button type="button" variant="ghost" size="sm" @click="cancelAvatar">Отмена</Button>
                </template>
            </div>
        </div>

        <Heading variant="small" title="Общая информация" />

        <Form v-bind="ProfileController.update.form()" class="space-y-6" v-slot="{ errors, processing }">
            <div class="grid gap-2">
                <Label for="name">Имя</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    placeholder="Полное имя"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    placeholder="Email"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="phone">Номер телефона</Label>
                <Input
                    id="phone"
                    class="mt-1 block w-full"
                    name="phone"
                    :default-value="user.phone ?? undefined"
                    autocomplete="tel"
                    placeholder="+7 (999) 000-00-00"
                />
                <InputError class="mt-2" :message="errors.phone" />
            </div>

            <div class="grid gap-2">
                <Label for="contacts">Другие контакты</Label>
                <Input
                    id="contacts"
                    class="mt-1 block w-full"
                    name="contacts"
                    :default-value="user.contacts ?? undefined"
                    placeholder="Telegram, VK и др."
                />
                <InputError class="mt-2" :message="errors.contacts" />
            </div>

            <div v-if="mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    Ваш адрес электронной почты не подтвержден.
                    <Link :href="send()" as="button" class="link-text">
                        Нажмите здесь, чтобы повторно отправить письмо с подтверждением.
                    </Link>
                </p>

                <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                    На ваш электронный адрес отправлена новая ссылка для подтверждения.
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <Label class="text-base font-medium">Режим невидимки</Label>
                <div class="flex items-center gap-3">
                    <Checkbox
                        id="is_invisible"
                        name="is_invisible"
                        :default-checked="user.is_invisible ?? false"
                        value="1"
                    />
                    <Label for="is_invisible" class="font-normal text-muted-foreground">
                        Скрыть себя из списков участников и запретить приглашения
                    </Label>
                </div>
                <InputError :message="errors.is_invisible" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" data-test="update-profile-button">Сохранить</Button>
            </div>
        </Form>
    </div>

    <DeleteUser />

    <AvatarCropModal v-if="cropOpen && cropSrc" :src="cropSrc" @confirm="onCropConfirm" @cancel="onCropCancel" />
</template>
