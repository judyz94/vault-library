import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import api from "../../api/api";
import { ArrowUturnLeftIcon } from '@heroicons/react/24/solid';

export default function Borrowing() {
    const [users, setUsers] = useState([]);
    const [books, setBooks] = useState([]);
    const [borrowedBooks, setBorrowedBooks] = useState([]);
    const [selectedUser, setSelectedUser] = useState("");
    const [alert, setAlert] = useState(null);
    const [loading, setLoading] = useState(false);

    const { register, handleSubmit, reset, formState: { errors } } = useForm();

    const showAlert = (msg, type = "info") => {
        setAlert({ msg, type });
        setTimeout(() => setAlert(null), 3500);
    };

    // Load users and available books
    useEffect(() => {
        (async () => {
            try {
                const [userRes, bookRes] = await Promise.all([
                    api.get("/users"),
                    api.get("/books"),
                ]);
                setUsers(userRes.data.data);
                setBooks(bookRes.data.data);
            } catch (err) {
                console.error(err);
                showAlert("Error loading data", "error");
            }
        })();
    }, []);

    // Load borrowings from the selected user
    const fetchBorrowedBooks = async (userId) => {
        try {
            setLoading(true);
            const res = await api.get(`/users/${userId}/borrowed`);
            setBorrowedBooks(res.data.data);
        } catch (err) {
            console.error(err);
            showAlert("Error loading borrowed books", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleBorrow = async (data) => {
        if (!selectedUser) {
            showAlert("Select a user first", "error");
            return;
        }

        try {
            const res = await api.post(`/users/${selectedUser}/borrow`, {
                book_id: data.book_id,
            });
            showAlert("Book borrowed successfully", "success");
            fetchBorrowedBooks(selectedUser);
            reset();
        } catch (err) {
            console.error(err);
            const msg =
                err.response?.data?.message || "Error borrowing book";
            showAlert(msg, "error");
        }
    };

    const handleReturn = async (bookId) => {
        if (!selectedUser) return;
        try {
            await api.post(`/users/${selectedUser}/return`, { book_id: bookId });
            showAlert("Book returned successfully", "success");
            await fetchBorrowedBooks(selectedUser);
        } catch (err) {
            console.error(err);
            showAlert("Error returning book", "error");
        }
    };

    return (
        <div className="p-6 bg-neutral-900 border border-neutral-800 rounded-2xl shadow-xl text-neutral-200">
            <h3 className="text-xl font-semibold text-cyan-400 mb-6">Borrowing Management</h3>

            {/* Alert */}
            {alert && (
                <div
                    className={`fixed top-20 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl text-sm font-medium shadow-lg
                        ${
                        alert.type === "success"
                            ? "bg-emerald-800/70 text-emerald-100 border border-emerald-500/40"
                            : alert.type === "error"
                                ? "bg-red-500/70 text-neutral-100 border border-red-500/40"
                                : "bg-cyan-500/60 text-neutral-900 border border-cyan-500/30"
                    }`}
                >
                    {alert.msg}
                </div>
            )}

            {/* Select User */}
            <div className="mb-4">
                <label className="text-sm text-neutral-400">Select User</label>
                <select
                    value={selectedUser}
                    onChange={(e) => {
                        setSelectedUser(e.target.value);
                        if (e.target.value) fetchBorrowedBooks(e.target.value);
                    }}
                    className="w-full mt-1 px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg focus:border-cyan-400 outline-none"
                >
                    <option value="">-- Choose a user --</option>
                    {users.map((u) => (
                        <option key={u.id} value={u.id}>
                            {u.name} ({u.email})
                        </option>
                    ))}
                </select>
            </div>

            {/* Borrow Form */}
            {selectedUser && (
                <form onSubmit={handleSubmit(handleBorrow)} className="space-y-4 mb-6">
                    <label className="text-sm text-neutral-400">Select Book to Borrow</label>
                    <select
                        {...register("book_id", { required: "Please select a book" })}
                        className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg focus:border-cyan-400 outline-none"
                        defaultValue=""
                    >
                        <option value="" disabled>Select book</option>
                        {books
                            .filter((b) => b.available)
                            .map((book) => (
                                <option key={book.id} value={book.id}>
                                    {book.title} ({book.author})
                                </option>
                            ))}
                    </select>
                    {errors.book_id && (
                        <p className="text-red-500 text-xs mt-1">{errors.book_id.message}</p>
                    )}

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            className="px-4 py-2 rounded-lg text-sm font-medium
                            bg-gradient-to-r from-cyan-500 to-emerald-400 text-neutral-900
                            hover:scale-105 transition"
                        >
                            Borrow
                        </button>
                    </div>
                </form>
            )}

            {/* Borrowed Books */}
            {selectedUser && (
                <div>
                    <h4 className="text-lg text-cyan-300 mb-3">Borrowed Books</h4>
                    {loading ? (
                        <p className="text-neutral-400">Loading...</p>
                    ) : borrowedBooks.length === 0 ? (
                        <p className="text-neutral-500 text-sm">No borrowed books found.</p>
                    ) : (
                        <table className="w-full text-sm border border-neutral-800 rounded-lg overflow-hidden">
                            <thead className="bg-neutral-800/70 text-neutral-400">
                            <tr>
                                <th className="px-4 py-2 text-left">Title</th>
                                <th className="px-4 py-2 text-left">Borrowed At</th>
                                <th className="px-4 py-2 text-left">Due Date</th>
                                <th className="px-4 py-2 text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            {borrowedBooks.map((b) => (
                                <tr key={b.id} className="border-t border-neutral-800">
                                    <td className="px-4 py-2">{b.book.title}</td>
                                    <td className="px-4 py-2">
                                        {new Date(b.borrowed_at).toLocaleDateString('en-CA')}
                                    </td>
                                    <td className="px-4 py-2">
                                        {new Date(b.due_at).toLocaleDateString('en-CA')}
                                    </td>
                                    <td className="px-4 py-2 text-center">
                                        <button
                                            onClick={() => handleReturn(b.book.id)}
                                            className="px-3 py-1 text-sm rounded-lg font-medium
                                            bg-gray-500/70 hover:bg-gray-500 text-white transition"
                                        >
                                            Return
                                        </button>
                                    </td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    )}
                </div>
            )}
        </div>
    );
}
