<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Trash2 } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import type { Post } from '@/types/feed';
import type { Paginated } from '@/types/pagination';

type Props = {
    posts: Paginated<Post>;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Публикации', href: '/admin/posts' },
        ],
    },
});

function formatDate(iso: string | null): string {
    if (!iso) {
return '—';
}

    return new Date(iso).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' });
}

function deletePost(id: number): void {
    if (confirm('Удалить публикацию?')) {
        router.delete(`/admin/posts/${id}`);
    }
}
</script>

<template>
    <Head title="Публикации" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Публикации</h1>
                <p class="text-sm text-muted-foreground">Управление публикациями клуба</p>
            </div>
            <Button as-child>
                <Link href="/admin/posts/create">
                    <Plus class="h-4 w-4" />
                    Добавить
                </Link>
            </Button>
        </div>

        <div v-if="posts.data.length === 0" class="empty-state">
            <p class="text-muted-foreground">Публикаций пока нет</p>
            <Button class="mt-4" as-child>
                <Link href="/admin/posts/create">Добавить первую публикацию</Link>
            </Button>
        </div>

        <div v-else class="flex flex-col gap-4">
            <div class="panel-table">
                <table>
                    <thead>
                        <tr>
                            <th class="col-th text-left">Заголовок</th>
                            <th class="col-th text-left">Автор</th>
                            <th class="col-th text-left">Опубликовано</th>
                            <th class="col-th text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="post in posts.data" :key="post.id">
                            <td class="col-td font-medium">
                                <span class="line-clamp-1">{{ post.title }}</span>
                            </td>
                            <td class="col-td text-muted-foreground">{{ post.author?.name ?? '—' }}</td>
                            <td class="col-td text-muted-foreground">{{ formatDate(post.published_at) }}</td>
                            <td class="col-td">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="icon" as-child>
                                        <Link :href="`/admin/posts/${post.id}/edit`">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="outline" size="icon" class="btn-danger" @click="deletePost(post.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :links="posts.links" />
        </div>
    </div>
</template>
