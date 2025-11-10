import { useEffect, useState } from "react";
import api from "../../api/api";
import {useAuth} from "../../context/AuthContext.jsx";

export default function UserBorrowing({ authUser }) {
    const [borrowedBooks, setBorrowedBooks] = useState([]);
    const [loading, setLoading] = useState(false);
    const [alert, setAlert] = useState(null);
    const { user } = useAuth();

    const showAlert = (msg, type = "info") => {
        setAlert({ msg, type });
        setTimeout(() => setAlert(null), 3500);
    };

    const fetchBorrowedBooks = async () => {
        if (!user?.id) return;

        try {
            setLoading(true);
            const res = await api.get(`/users/${user.id}/borrowed`);
            setBorrowedBooks(res.data.data || []);
        } catch (err) {
            console.error(err);
            showAlert("Error loading borrowed books", "error");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchBorrowedBooks();
    }, [user]);

    return (
        <div className="p-6 bg-neutral-900 rounded-2xl shadow-md border border-neutral-800">
            <h3 className="text-xl font-semibold text-cyan-400 mb-6 tracking-tight">
                My Borrowed Books
            </h3>

            {/* Alert */}
            {alert && (
                <div
                    className={`fixed top-20 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl text-sm font-medium shadow-lg
                        ${
                        alert.type === "success"
                            ? "bg-emerald-500/60 text-emerald-100 border border-emerald-500/40"
                            : alert.type === "error"
                                ? "bg-red-500/70 text-neutral-100 border border-red-500/40"
                                : "bg-cyan-500/60 text-neutral-900 border border-cyan-500/30"
                    }`}
                >
                    {alert.msg}
                </div>
            )}

            {loading ? (
                <p className="text-neutral-400">Loading borrowed books...</p>
            ) : borrowedBooks.length === 0 ? (
                <p className="text-neutral-500 text-sm">
                    You currently have no borrowed books.
                </p>
            ) : (
                <table className="w-full text-sm border border-neutral-800 rounded-lg overflow-hidden">
                    <thead className="bg-neutral-800/70 text-neutral-400">
                    <tr>
                        <th className="px-4 py-2 text-left">Title</th>
                        <th className="px-4 py-2 text-left">Borrowed At</th>
                        <th className="px-4 py-2 text-left">Due Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    {borrowedBooks.map((b) => (
                        <tr
                            key={b.id}
                            className="border-t border-neutral-800 hover:bg-neutral-800/40 transition"
                        >
                            <td className="px-4 py-2 text-neutral-200">
                                {b.book.title}
                            </td>
                            <td className="px-4 py-2 text-neutral-400">
                                {new Date(b.borrowed_at).toLocaleDateString('en-CA')}
                            </td>
                            <td className="px-4 py-2 text-neutral-400">
                                {new Date(b.due_at).toLocaleDateString('en-CA')}
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}
        </div>
    );
}
