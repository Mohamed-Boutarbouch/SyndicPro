"use client";

import { createContext, useContext } from "react";
import useSWR from "swr";
import { fetchSyndic } from "@/services/fetchSyndic";
import type { User } from "@/types/syndic";

const SyndicContext = createContext<User | null>(null);

export function SyndicProvider({ children }: { children: React.ReactNode }) {
  const { data, error, isLoading } = useSWR<User>(
    "/users/syndic/1/building",
    () => fetchSyndic(1)
  );

  return (
    <SyndicContext.Provider value={data ?? null}>
      {children}
    </SyndicContext.Provider>
  );
}

export function useSyndic() {
  return useContext(SyndicContext);
}
