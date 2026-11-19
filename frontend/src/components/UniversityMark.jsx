
import { useState } from "react";
import { University } from "lucide-react";

export default function UniversityMark({
  size = "normal",
}) {
  const [imageFailed, setImageFailed] =
    useState(false);

  const dimensions =
    size === "small"
      ? "h-12 w-12"
      : "h-16 w-16";

  return (
    <div
      className={`
        ${dimensions}
        flex shrink-0 items-center justify-center
        rounded-2xl bg-white p-1.5 shadow-sm
      `}
    >
      {imageFailed ? (
        <University
          className="text-brand"
          size={size === "small" ? 25 : 34}
          aria-label="University"
          role="img"
        />
      ) : (
        <img
          src="/usj-emblem.png"
          alt="University of Sri Jayewardenepura"
          className="h-full w-full object-contain"
          onError={() => setImageFailed(true)}
        />
      )}
    </div>
  );
}
