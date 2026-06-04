export type ReservationTable = {
    id: number;
    name: string;
    description: string | null;
    status: 'ready' | 'not_ready';
};

export type ReservationUser = {
    id: number;
    name: string;
    email: string;
};

export type Reservation = {
    id: number;
    date: string;
    comment: string | null;
    table_id: number | null;
    table: ReservationTable | null;
    users: ReservationUser[];
    created_at: string;
    updated_at: string;
};

export type BookingRequestStatus = 'pending' | 'approved' | 'rejected';

export type InviteStatus = 'pending' | 'accepted' | 'revoked' | 'expired';

export type Invite = {
    id: number;
    status: InviteStatus;
    author_id: number;
    target_id: number;
    reservation_id: number;
    author: ReservationUser | null;
    target: ReservationUser | null;
    reservation: (Reservation & { table: ReservationTable | null }) | null;
    created_at: string;
};

export type BookingRequest = {
    id: number;
    author_id: number;
    author: ReservationUser | null;
    date: string;
    comment: string | null;
    table_id: number | null;
    table: ReservationTable | null;
    users: ReservationUser[];
    status: number;
    created_at: string;
    updated_at: string;
};
