export type Role = "ROLE_ADMIN" | "ROLE_USER" | string;

export interface User {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
  fullName: string;
  phoneNumber: string;
  roles: Role[];
  building: Building;
}

export interface Building {
  id: number;
  address: string;
  description: string;
  name: string;
}

