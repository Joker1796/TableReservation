export type FeedItemType = 'post';

export type PostFeedItem = {
    type: Extract<FeedItemType, 'post'>;
    id: number;
    title: string;
    content: string;
    author: { id: number; name: string };
    published_at: string;
    created_at: string;
};

export type FeedItem = PostFeedItem;

export type CursorPage<T> = {
    data: T[];
    next_cursor: string | null;
    next_page_url: string | null;
};

export type Post = {
    id: number;
    title: string;
    content: string;
    author_id: number;
    author: { id: number; name: string } | null;
    published_at: string | null;
    created_at: string;
    updated_at: string;
};
