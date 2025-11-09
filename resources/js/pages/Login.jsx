import { useForm } from "react-hook-form";
import { useAuth } from "../context/AuthContext";
import {useNavigate} from "react-router-dom";

export default function Login() {
    const { register, handleSubmit, formState: { errors, isSubmitting } } = useForm();
    const { login } = useAuth();
    const navigate = useNavigate();

    const onSubmit = async (data) => {
        try {
            await login(data.email, data.password);

            navigate("/dashboard");
        } catch (error) {
            alert(error.response?.data?.message || "Login failed");
        }
    };

    return (
        <div className="flex items-center justify-center min-h-screen bg-neutral-900 text-neutral-200">
            <form
                onSubmit={handleSubmit(onSubmit)}
                className="bg-neutral-800 border border-neutral-700 rounded-2xl shadow-lg p-10 w-full max-w-md
                           transition-all duration-300 hover:shadow-[0_0_25px_rgba(6,182,212,0.2)]"
            >
                <h1 className="text-3xl font-semibold text-center text-cyan-400 mb-8 tracking-tight">
                    Vault Library
                </h1>

                <div className="mb-6">
                    <label className="block text-sm font-medium text-neutral-400 mb-2">
                        Email
                    </label>
                    <input
                        type="email"
                        {...register("email", { required: "Email is required" })}
                        className="w-full px-4 py-2 bg-neutral-900 border border-neutral-700 rounded-lg text-neutral-100
                                   placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition"
                        placeholder="you@example.com"
                    />
                    {errors.email && (
                        <p className="text-red-400 text-xs mt-1">{errors.email.message}</p>
                    )}
                </div>

                <div className="mb-8">
                    <label className="block text-sm font-medium text-neutral-400 mb-2">
                        Password
                    </label>
                    <input
                        type="password"
                        {...register("password", { required: "Password is required" })}
                        className="w-full px-4 py-2 bg-neutral-900 border border-neutral-700 rounded-lg text-neutral-100
                                   placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                        placeholder="••••••••"
                    />
                    {errors.password && (
                        <p className="text-red-400 text-xs mt-1">{errors.password.message}</p>
                    )}
                </div>

                <button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full py-3 rounded-xl font-medium text-neutral-900
                               bg-gradient-to-r from-cyan-500 to-emerald-400
                               hover:shadow-[0_0_20px_rgba(6,182,212,0.4)] hover:scale-[1.02] transition-all duration-300"
                >
                    {isSubmitting ? "Logging in..." : "Login"}
                </button>
            </form>
        </div>
    );
}
