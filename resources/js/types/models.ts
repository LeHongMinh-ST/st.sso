export interface Faculty {
  id: number;
  name: string;
  code: string;
  status: "active" | "inactive";
  logo?: string;
  created_at: string;
  updated_at: string;
}

export interface Department {
  id: number;
  name: string;
  code: string;
  status: "active" | "inactive";
  faculty_id: number;
  created_at: string;
  updated_at: string;
}

export interface User {
  id: number;
  name: string;
  first_name: string;
  last_name: string;
  email: string;
  role: "super_admin" | "officer" | "teacher" | "student";
  status: "active" | "inactive";
  code: string;
  department_id?: number;
  faculty_id?: number;
  created_at: string;
  updated_at: string;
}