import {
  Ticket,
  ShieldCheck,
  UsersRound,
  KeyRound,
  CheckCircle2,
} from "lucide-react";

const modules = [
  {
    title: "User Management",
    description: "Manage system users and profiles.",
    icon: UsersRound,
  },
  {
    title: "Role Management",
    description: "Create and manage dynamic roles.",
    icon: ShieldCheck,
  },
  {
    title: "Permissions",
    description: "Control access to system features.",
    icon: KeyRound,
  },
];

export default function App() {
  return (
    <div className="min-h-screen bg-slate-50 text-slate-900">
      {/* Header */}
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-6xl items-center gap-3 px-5 py-5">
          <div className="rounded-xl bg-slate-900 p-2.5 text-white">
            <Ticket size={24} />
          </div>

          <div>
            <h1 className="text-lg font-bold">Ticketing System</h1>

            <p className="text-xs text-slate-500">Administration Portal</p>
          </div>
        </div>
      </header>

      {/* Main */}
      <main className="mx-auto max-w-6xl px-5 py-12 sm:py-20">
        <div className="max-w-2xl">
          <div className="mb-5 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700">
            <CheckCircle2 size={17} />
            Frontend setup
          </div>

          <h2 className="text-3xl font-bold tracking-tight sm:text-5xl">
            Welcome to your
            <span className="block text-indigo-600">Ticketing System</span>
          </h2>

          <p className="mt-5 text-base leading-7 text-slate-600">
            A simple and secure administration platform for managing users,
            roles, permissions, and tickets.
          </p>
        </div>

        {/* Modules */}
        <div className="mt-12 grid gap-5 md:grid-cols-3">
          {modules.map((module) => {
            const Icon = module.icon;

            return (
              <div
                key={module.title}
                className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md"
              >
                <div className="mb-5 inline-flex rounded-xl bg-indigo-50 p-3 text-indigo-600">
                  <Icon size={24} />
                </div>

                <h3 className="text-lg font-semibold">{module.title}</h3>

                <p className="mt-2 text-sm leading-6 text-slate-500">
                  {module.description}
                </p>
              </div>
            );
          })}
        </div>

        {/* Footer */}
        <p className="mt-12 text-center text-sm text-slate-400">
          Ticketing Management System
        </p>
      </main>
    </div>
  );
}
