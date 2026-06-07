import { onMounted, onUnmounted, ref  } from 'vue';
import type {Ref} from 'vue';

export function useInfiniteScroll<T>(
    initialItems: T[],
    initialCursor: string | null,
    apiUrl: string,
): { items: Ref<T[]>; loading: Ref<boolean>; hasMore: Ref<boolean>; sentinel: Ref<HTMLElement | null> } {
    const items = ref<T[]>([...initialItems]) as Ref<T[]>;
    const cursor = ref<string | null>(initialCursor);
    const loading = ref(false);
    const hasMore = ref(initialCursor !== null);
    const sentinel = ref<HTMLElement | null>(null);

    async function loadMore(): Promise<void> {
        if (loading.value || !hasMore.value || !cursor.value) {
return;
}

        loading.value = true;

        try {
            const url = `${apiUrl}?cursor=${encodeURIComponent(cursor.value)}`;
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
            });
            const data = await response.json();
            items.value.push(...(data.data as T[]));
            cursor.value = data.next_cursor ?? null;
            hasMore.value = data.next_cursor !== null;
        } finally {
            loading.value = false;
        }
    }

    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        if (!sentinel.value) {
return;
}

        observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
void loadMore();
}
        });
        observer.observe(sentinel.value);
    });

    onUnmounted(() => {
        observer?.disconnect();
    });

    return { items, loading, hasMore, sentinel };
}
