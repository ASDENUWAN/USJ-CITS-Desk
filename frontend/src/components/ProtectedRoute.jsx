
import {
  Navigate,
  Outlet,
} from "react-router";

import { useAuth } from "../context/AuthContext";

export default function ProtectedRoute() {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="flex min-h-screen flex-col items-center justify-center gap-4 bg-app-bg">
        <div className="h-10 w-10 animate-spin rounded-full border-4 border-red-100 border-t-brand" />

        <p
          role="status"
          className="text-sm text-slate-600"
        >
          Checking your session...
        </p>
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  return <Outlet />;
}
