
import { useState } from "react";
import { useNavigate } from "react-router";

import {
  ArrowRight,
  KeyRound,
  LogOut,
  ShieldCheck,
  Ticket,
  UserRound,
  UsersRound,
} from "lucide-react";

import { useAuth } from "../../context/AuthContext";
import UniversityMark from "../../components/UniversityMark";

const modules = [
  {
    title: "User Management",
    description: "Manage users and their account details.",
    icon: UsersRound,
  },
  {
    title: "Role Management",
    description: "Organize access using dynamic roles.",
    icon: ShieldCheck,
  },
  {
    title: "Permissions",
    description: "Manage fine-grained access permissions.",
    icon: KeyRound,
  },
];

export default function DashboardPage() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const [loggingOut, setLoggingOut] =
    useState(false);

  const [error, setError] = useState("");

  async function handleLogout() {
    try {
      setLoggingOut(true);
      setError("");

      await logout();

      navigate("/login", { replace: true });
    } catch {
      setError(
        "Unable to sign out. Please try again."
      );
    } finally {
      setLoggingOut(false);
    }
  }

  const roles = user?.roles ?? [];

  return (
    <div className="min-h-screen bg-app-bg text-slate-900">

      {/* TOP NAVIGATION */}
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4">

          <div className="flex min-w-0 items-center gap-3">
            <UniversityMark size="small" />

            <div className="min-w-0">
              <h1 className="truncate text-lg font-bold">
                USJ CITS Desk
              </h1>
              <p className="text-xs text-slate-500">
                Administration Portal
              </p>
            </div>
          </div>

          <button
            onClick={handleLogout}
            disabled={loggingOut}
            className="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-brand disabled:opacity-60"
          >
            <LogOut size={17} />
            <span className="hidden sm:inline">
              {loggingOut ? "Signing out..." : "Sign out"}
            </span>
          </button>
        </div>
      </header>

      <main className="mx-auto max-w-6xl px-5 py-8 sm:py-12">

        {error && (
          <div
            role="alert"
            className="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-800"
          >
            {error}
          </div>
        )}

        {/* WELCOME PANEL */}
        <section className="relative overflow-hidden rounded-3xl bg-brand-deep p-7 text-white sm:p-10">

          <div
            aria-hidden="true"
            className="absolute -right-16 -top-20 h-64 w-64 rounded-full border border-white/15"
          />

          <div className="relative">
            <div className="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium text-amber-200">
              <ShieldCheck size={15} />
              Authenticated session
            </div>

            <h2 className="text-2xl font-bold sm:text-3xl">
              Welcome, {user?.name || user?.username}
            </h2>

            <p className="mt-3 max-w-xl text-sm leading-7 text-white/75">
              You're signed in to USJ CITS Desk.
              Manage your account and access the
              administration modules available to you.
            </p>
          </div>
        </section>

        {/* ACCOUNT DETAILS */}
        <section className="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <div className="flex items-start gap-4">
            <div className="rounded-xl bg-red-50 p-3 text-brand">
              <UserRound size={24} />
            </div>

            <div className="min-w-0 flex-1">
              <h3 className="text-lg font-semibold">
                Your account
              </h3>

              <p className="mt-1 text-sm text-slate-500">
                Signed in as @{user?.username}
              </p>

              <div className="mt-4 flex flex-wrap gap-2">
                {roles.length ? (
                  roles.map((role) => (
                    <span
                      key={role}
                      className="rounded-full bg-red-50 px-3 py-1.5 text-xs font-medium text-brand"
                    >
                      {role}
                    </span>
                  ))
                ) : (
                  <span className="text-sm text-slate-500">
                    No role assigned
                  </span>
                )}
              </div>
            </div>
          </div>
        </section>

        {/* MODULES */}
        <section className="mt-10">

          <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
              <h3 className="text-xl font-bold">
                Management modules
              </h3>
              <p className="mt-1 text-sm text-slate-500">
                These frontend modules will be connected next.
              </p>
            </div>

            <span className="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-xs text-slate-500 ring-1 ring-slate-200">
              <Ticket size={15} />
              Setup in progress
            </span>
          </div>

          <div className="grid gap-5 md:grid-cols-3">
            {modules.map((module) => {
              const Icon = module.icon;

              return (
                <div
                  key={module.title}
                  className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                  <div className="mb-5 inline-flex rounded-xl bg-red-50 p-3 text-brand">
                    <Icon size={24} />
                  </div>

                  <h4 className="text-base font-semibold">
                    {module.title}
                  </h4>

                  <p className="mt-2 min-h-12 text-sm leading-6 text-slate-500">
                    {module.description}
                  </p>

                  <div className="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4 text-xs font-medium text-slate-400">
                    Coming next
                    <ArrowRight size={14} />
                  </div>
                </div>
              );
            })}
          </div>
        </section>

        <footer className="mt-12 border-t border-slate-200 pt-6 text-center text-xs text-slate-400">
          USJ CITS Desk — University of Sri Jayewardenepura
        </footer>

      </main>
    </div>
  );
}
