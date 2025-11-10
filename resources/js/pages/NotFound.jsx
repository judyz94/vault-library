import { Link } from "react-router-dom";

export default function NotFound() {
    return (
        <div className="flex items-center justify-center min-h-screen bg-neutral-900 text-neutral-200">
            <div className="text-center p-10 bg-neutral-800 rounded-2xl shadow-lg max-w-md w-full border border-neutral-700">
                <h1 className="text-7xl font-extrabold text-cyan-400 mb-4 tracking-tight">
                    404
                </h1>
                <p className="text-lg text-neutral-400 mb-8">
                    Oops! The page you’re looking for doesn’t exist or has been moved.
                </p>

                <Link
                    to="/dashboard"
                    className="inline-block px-6 py-3 rounded-xl font-medium transition-all duration-300
                               bg-gradient-to-r from-cyan-500 to-emerald-400 text-neutral-900
                               hover:shadow-[0_0_15px_rgba(6,182,212,0.5)] hover:scale-105"
                >
                    Go Back Home
                </Link>
            </div>
        </div>
    );
}
