import { api } from "@/lib/axios";
import type { User } from "@/types/syndic";

export async function fetchSyndic(userId: number): Promise<User> {
  const { data } = await api.get<User>(`/users/syndic/${userId}/building`);
  return data;
}
