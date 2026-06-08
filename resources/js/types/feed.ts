export type FeedItemType = 'post' | 'poll';

export type PostFeedItem = {
    type: Extract<FeedItemType, 'post'>;
    id: number;
    title: string;
    content: string;
    author: { id: number; name: string };
    published_at: string;
    created_at: string;
};

export type PollOption = {
    id: number;
    text: string;
    sort_order: number;
    votes_count: number;
};

export type PollFeedItem = {
    type: Extract<FeedItemType, 'poll'>;
    id: number;
    question: string;
    description: string | null;
    allow_multiple: boolean;
    author: { id: number; name: string } | null;
    published_at: string;
    closes_at: string | null;
    is_open: boolean;
    has_voted: boolean;
    user_vote_ids: number[];
    total_votes: number;
    options: PollOption[];
};

export type FeedItem = PostFeedItem | PollFeedItem;

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
    is_suggestion: boolean;
    created_at: string;
    updated_at: string;
};

export type PostSuggestion = {
    id: number;
    title: string;
    author: { id: number; name: string } | null;
};
