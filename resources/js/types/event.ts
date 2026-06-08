export type Event = {
    id: number;
    title: string;
    short_description: string | null;
    description: string | null;
    starts_at: string | null;
    ends_at: string | null;
    is_suggestion: boolean;
    author_id: number;
    author: { id: number; name: string } | null;
    participants: { id: number; name?: string; phone?: string | null; contacts?: string | null }[];
    created_at: string;
    updated_at: string;
};

export type EventSuggestion = {
    id: number;
    title: string;
    author: { id: number; name: string } | null;
};

export type EventRegistration = {
    id: number;
    user_name: string;
    event_title: string;
    created_at: string;
};
