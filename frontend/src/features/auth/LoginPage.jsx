
import { useState } from "react";
import { useNavigate } from "react-router";

import {
  ArrowRight,
  Eye,
  EyeOff,
  LockKeyhole,
  ShieldCheck,
  TicketCheck,
  UserRound,
  CheckCircle2,
} from "lucide-react";

import { useAuth } from "../../context/AuthContext";
import UniversityMark from "../../components/UniversityMark";

export default function LoginPage() {
  const navigate = useNavigate();
  const { login } = useAuth();

  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  const [error, setError] = useState("");

  async function handleSubmit(event) {
    event.preventDefault();
    setError("");

    if (!username.trim() || !password) {
      setError("Enter your username and password.");
      return;
    }

    try {
      setSubmitting(true);

      await login(username.trim(), password);

      navigate("/", { replace: true });
    } catch (err) {
      const status = err.response?.status;

      if (status === 422 || status === 401) {
        setError("Invalid username or password.");
      } else if (status === 429) {
        setError(
          "Too many login attempts. Please try again later."
        );
      } else if (status === 419) {
        setError(
          "Your session expired. Please try signing in again."
        );
      } else {
        setError(
          "Unable to sign in. Please check your connection and try again."
        );
      }
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div className="min-h-screen bg-app-bg lg:grid lg:grid-cols-2">

      {/* LEFT BRANDING PANEL */}
      <section className="relative hidden min-h-screen overflow-hidden bg-gradient-to-br from-brand-deep via-[#680609] to-brand lg:flex lg:flex-col lg:justify-between p-12 xl:p-16">

        {/* Decorative shapes */}
        <div
          aria-hidden="true"
          className="pointer-events-none absolute -right-36 -top-36 h-96 w-96 rounded-full border border-white/15"
        />
        <div
          aria-hidden="true"
          className="pointer-events-none absolute -bottom-40 -left-40 h-[430px] w-[430px] rounded-full border border-white/10"
        />

        <div className="relative flex items-center gap-4">
          <UniversityMark />

          <div>
            <h1 className="text-2xl font-bold tracking-tight text-white">
              USJ CITS Desk
            </h1>
            <p className="mt-1 text-sm text-white/70">
              University Systems Support Portal
            </p>
          </div>
        </div>

        <div className="relative max-w-xl">

          <div className="mb-7 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-amber-200">
            <ShieldCheck size={17} />
            Secure CITS Support Platform
          </div>

          <h2 className="text-4xl font-bold leading-tight tracking-tight text-white xl:text-5xl">
            One place to
            <span className="block text-brand-gold">
              manage support.
            </span>
          </h2>

          <p className="mt-6 max-w-md text-base leading-8 text-white/75">
            A centralized platform for managing
            support requests and technical issues
            related to systems developed and
            maintained by USJ CITS.
          </p>

          <div className="mt-10 grid max-w-lg gap-3">
            {[
              "Organized issue tracking",
              "Role-based access management",
              "Secure university system support",
            ].map((item) => (
              <div
                key={item}
                className="flex items-center gap-3 text-sm text-white/85"
              >
                <CheckCircle2
                  size={19}
                  className="shrink-0 text-brand-gold"
                />
                {item}
              </div>
            ))}
          </div>
        </div>

        <div className="relative border-t border-white/15 pt-6 text-sm leading-6 text-white/65">
          <p className="font-semibold text-white/90">
            Centre for IT Services
          </p>
          <p>
            University of Sri Jayewardenepura
          </p>
        </div>
      </section>

      {/* RIGHT LOGIN PANEL */}
      <section className="flex min-h-screen items-center justify-center px-5 py-10 sm:px-10 lg:px-12">

        <div className="w-full max-w-md">

          {/* Mobile branding */}
          <div className="mb-10 flex items-center gap-3 lg:hidden">
            <UniversityMark size="small" />

            <div>
              <h1 className="text-xl font-bold text-slate-900">
                USJ CITS Desk
              </h1>
              <p className="text-xs text-slate-500">
                University Systems Support Portal
              </p>
            </div>
          </div>

          <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-9 lg:shadow-md">

            <div className="mb-8">
              <div className="mb-5 inline-flex rounded-xl bg-red-50 p-3 text-brand">
                <LockKeyhole size={24} />
              </div>

              <h2 className="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Welcome back
              </h2>

              <p className="mt-2 text-sm leading-6 text-slate-500">
                Sign in using your assigned CITS Desk
                username and password.
              </p>
            </div>

            <form
              onSubmit={handleSubmit}
              className="space-y-5"
              noValidate
            >

              {/* Username */}
              <div>
                <label
                  htmlFor="username"
                  className="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Username
                </label>

                <div className="relative">
                  <UserRound
                    size={19}
                    aria-hidden="true"
                    className="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="username"
                    name="username"
                    type="text"
                    autoComplete="username"
                    autoCapitalize="none"
                    spellCheck={false}
                    maxLength={50}
                    required
                    value={username}
                    onChange={(e) =>
                      setUsername(e.target.value)
                    }
                    placeholder="Enter your username"
                    className="w-full rounded-xl border border-slate-300 bg-white py-3.5 pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-red-100"
                  />
                </div>
              </div>

              {/* Password */}
              <div>
                <label
                  htmlFor="password"
                  className="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Password
                </label>

                <div className="relative">
                  <LockKeyhole
                    size={19}
                    aria-hidden="true"
                    className="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="password"
                    name="password"
                    type={showPassword ? "text" : "password"}
                    autoComplete="current-password"
                    required
                    value={password}
                    onChange={(e) =>
                      setPassword(e.target.value)
                    }
                    placeholder="Enter your password"
                    className="w-full rounded-xl border border-slate-300 bg-white py-3.5 pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-red-100"
                  />

                  <button
                    type="button"
                    onClick={() =>
                      setShowPassword((value) => !value)
                    }
                    aria-label={
                      showPassword
                        ? "Hide password"
                        : "Show password"
                    }
                    aria-pressed={showPassword}
                    className="absolute right-4 top-1/2 -translate-y-1/2 rounded-md text-slate-500 hover:text-slate-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
                  >
                    {showPassword ? (
                      <EyeOff size={19} />
                    ) : (
                      <Eye size={19} />
                    )}
                  </button>
                </div>
              </div>

              {/* Login error */}
              {error && (
                <div
                  role="alert"
                  className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-6 text-red-800"
                >
                  {error}
                </div>
              )}

              {/* Submit */}
              <button
                type="submit"
                disabled={submitting}
                className="flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:cursor-not-allowed disabled:opacity-60"
              >
                {submitting ? (
                  <>
                    <span className="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" />
                    Signing in...
                  </>
                ) : (
                  <>
                    Sign in
                    <ArrowRight size={18} />
                  </>
                )}
              </button>
            </form>

            <div className="mt-7 border-t border-slate-100 pt-6">
              <div className="flex items-start gap-3">
                <TicketCheck
                  size={19}
                  className="mt-0.5 shrink-0 text-brand"
                />
                <p className="text-xs leading-6 text-slate-500">
                  Access is restricted to authorized
                  users. Contact your system administrator
                  if you need an account.
                </p>
              </div>
            </div>
          </div>

          <p className="mt-8 text-center text-xs leading-5 text-slate-400">
            USJ CITS Desk
            <span className="mx-2">•</span>
            University of Sri Jayewardenepura
          </p>
        </div>
      </section>
    </div>
  );
}
