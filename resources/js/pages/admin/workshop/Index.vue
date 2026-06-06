<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ImageUp, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import type { WorkshopPhoto } from '@/types/workshop';

type Props = {
    photos: WorkshopPhoto[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Админ', href: '/admin/requests' },
            { title: 'Мастерская', href: '/admin/workshop' },
        ],
    },
});

const fileInput = ref<HTMLInputElement | null>(null);
const dragOver = ref(false);
const selectedFiles = ref<File[]>([]);
const uploading = ref(false);
const uploadError = ref<string | null>(null);

function openPicker(): void {
    fileInput.value?.click();
}

function onFileInputChange(e: Event): void {
    const target = e.target as HTMLInputElement;
    addFiles(Array.from(target.files ?? []));
}

function onDragOver(e: DragEvent): void {
    e.preventDefault();
    dragOver.value = true;
}

function onDragLeave(): void {
    dragOver.value = false;
}

function onDrop(e: DragEvent): void {
    e.preventDefault();
    dragOver.value = false;
    addFiles(Array.from(e.dataTransfer?.files ?? []));
}

function addFiles(files: File[]): void {
    const images = files.filter((f) => f.type.startsWith('image/'));

    if (images.length < files.length) {
        uploadError.value = 'Некоторые файлы пропущены — допустимы только изображения';
    } else {
        uploadError.value = null;
    }

    const existing = new Set(selectedFiles.value.map((f) => f.name + f.size));
    const newOnes = images.filter((f) => !existing.has(f.name + f.size));
    selectedFiles.value = [...selectedFiles.value, ...newOnes];
}

function removeFile(index: number): void {
    selectedFiles.value = selectedFiles.value.filter((_, i) => i !== index);

    if (selectedFiles.value.length === 0 && fileInput.value) {
        fileInput.value.value = '';
    }
}

function upload(): void {
    if (selectedFiles.value.length === 0) {
        return;
    }

    const data = new FormData();

    for (const file of selectedFiles.value) {
        data.append('photos[]', file);
    }

    uploading.value = true;
    router.post('/admin/workshop', data, {
        onSuccess: () => {
            selectedFiles.value = [];
            uploadError.value = null;

            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
        onError: (errors) => {
            uploadError.value = errors['photos'] ?? errors['photos.0'] ?? null;
        },
        onFinish: () => {
            uploading.value = false;
        },
    });
}

function deletePhoto(id: number): void {
    if (confirm('Удалить фото?')) {
        router.delete(`/admin/workshop/${id}`);
    }
}
</script>

<template>
    <Head title="Мастерская — фотографии" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-semibold">Мастерская</h1>
            <p class="text-sm text-muted-foreground">Фотографии работ мастерской</p>
        </div>

        <!-- Drop zone -->
        <div>
            <input ref="fileInput" type="file" accept="image/*" multiple class="hidden" @change="onFileInputChange" />

            <div
                class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed p-10 transition-colors"
                :class="
                    dragOver ? 'border-primary bg-primary/5' : 'border-border hover:border-primary/50 hover:bg-muted/40'
                "
                @click="openPicker"
                @dragover="onDragOver"
                @dragleave="onDragLeave"
                @drop="onDrop"
            >
                <ImageUp class="mb-3 h-10 w-10 text-muted-foreground" />
                <p class="mb-1 text-sm font-medium">Перетащите изображения сюда</p>
                <p class="text-xs text-muted-foreground">или нажмите для выбора файлов</p>
                <p class="mt-2 text-xs text-muted-foreground">PNG, JPG, WEBP · до 5 МБ каждый</p>
            </div>

            <!-- Selected files list -->
            <ul v-if="selectedFiles.length > 0" class="mt-3 flex flex-col gap-2">
                <li
                    v-for="(file, index) in selectedFiles"
                    :key="file.name + file.size"
                    class="flex items-center justify-between rounded-lg border border-border bg-muted/30 px-4 py-2.5"
                >
                    <div class="flex items-center gap-3 overflow-hidden">
                        <ImageUp class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span class="truncate text-sm">{{ file.name }}</span>
                        <span class="shrink-0 text-xs text-muted-foreground">
                            {{ (file.size / 1024 / 1024).toFixed(2) }} МБ
                        </span>
                    </div>
                    <button
                        class="ml-3 shrink-0 text-muted-foreground hover:text-foreground"
                        @click="removeFile(index)"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </li>
            </ul>

            <p v-if="uploadError" class="mt-2 text-xs text-destructive">{{ uploadError }}</p>

            <Button v-if="selectedFiles.length > 0" class="mt-3" :disabled="uploading" @click="upload">
                {{
                    uploading
                        ? 'Загружается...'
                        : `Загрузить ${selectedFiles.length > 1 ? selectedFiles.length + ' файла' : 'файл'}`
                }}
            </Button>
        </div>

        <!-- Empty state -->
        <div v-if="photos.length === 0" class="empty-state">
            <p class="text-muted-foreground">Фотографий пока нет</p>
        </div>

        <!-- Photo grid -->
        <div v-else class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
            <div
                v-for="photo in photos"
                :key="photo.id"
                class="group relative overflow-hidden rounded-lg border border-border"
            >
                <img :src="photo.url" :alt="photo.original_name" class="aspect-square w-full object-cover" />
                <div
                    class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100"
                >
                    <Button variant="destructive" size="icon" @click="deletePhoto(photo.id)">
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
                <p class="truncate px-2 py-1.5 text-xs text-muted-foreground">{{ photo.original_name }}</p>
            </div>
        </div>
    </div>
</template>
