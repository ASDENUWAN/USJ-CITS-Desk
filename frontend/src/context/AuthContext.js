
import {
  createContext,
  useContext,
  useEffect,
  useState,
} from "react";

import api from "../api/client";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  // Restore authentication after page refresh.
  useEffect(() => {
    let active = true;

    async function loadUser() {
      try {
        const response = await api.get("/api/me");

        if (active) {
          setUser(response.data.user);
        }
      } catch {
        if (active) {
          setUser(null);
        }
      } finally {
        if (active) {
          setLoading(false);
        }
      }
    }

    loadUser();

    return () => {
      active = false;
    };
  }, []);

  async function login(username, password) {
    // Initialize Laravel CSRF protection.
    await api.get("/sanctum/csrf-cookie");

    // Authenticate using username and password.
    const response = await api.post("/api/login", {
      username,
      password,
    });

    setUser(response.data.user);

    return response.data.user;
  }

  async function logout() {
    await api.post("/api/logout");

    setUser(null);
  }

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        login,
        logout,
        isAuthenticated: Boolean(user),
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error(
      "useAuth must be used within AuthProvider"
    );
  }

  return context;
}
