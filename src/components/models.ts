export interface Todo {
  id: number;
  content: string;
}

export interface Meta {
  totalCount: number;
}

export interface Teacher {
  id: number;
  name: string;
  email: string;
  school_id: string;
  roles: string;
  is_focused?: boolean;
}

export interface Student {
  id: number;
  name: string;
  school_id: string
  roles: null;
  is_focued: boolean;
}

export interface School {
  id: number;
  name: string;
  description: string;
}
export interface Token {
  access_token: string;
  expires_in: number;
  refresh_token: string;
}
