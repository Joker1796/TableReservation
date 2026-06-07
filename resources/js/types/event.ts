export type Event = {
    id: number;
    title: string;
    short_description: string | null;
    description: string | null;
    starts_at: string;
    ends_at: string | null;
    author_id: number;
    author: { id: number; name: string } | null;
    participants: { id: number; name?: string }[];
    created_at: string;
    updated_at: string;
};

export type EventRegistration = {
    id: number;
    user_name: string;
    event_title: string;
    created_at: string;
};
