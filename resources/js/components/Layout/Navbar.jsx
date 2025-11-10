import { useNavigate } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";

export default function Navbar() {
    const { user, logout } = useAuth();
    const navigate = useNavigate();

    const handleLogout = async () => {
        await logout();
        navigate("/login");
    };

    return (
        <nav className="bg-neutral-900 border-b border-neutral-800 shadow-lg">
            <div className="flex justify-between items-center h-16 px-6">
                <div className="text-cyan-400 font-semibold tracking-tight text-lg"></div>

                <div className="flex items-center space-x-5">
                    <span className="text-neutral-300 text-sm font-medium">
                        {user?.name}
                    </span>

                    <button
                        onClick={handleLogout}
                        className="px-4 py-2 rounded-lg text-sm font-medium
                                   bg-gradient-to-r from-cyan-500 to-emerald-400
                                   text-neutral-900 hover:scale-105
                                   hover:shadow-[0_0_12px_rgba(6,182,212,0.4)]
                                   transition-all duration-300"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </nav>
    );
}
