import { useEffect, useState } from "react";
import api from "../../api/api";
import {useForm} from "react-hook-form";

export default function BooksTable() {
    const [books, setBooks] = useState([]);
    const [authors, setAuthors] = useState([]);
    const [search, setSearch] = useState("");
    const [currentPage, setCurrentPage] = useState(1);
    const [alert, setAlert] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [editingBook, setEditingBook] = useState(null);
    const itemsPerPage = 5;

    const {
        register,
        handleSubmit,
        reset,
        formState: { errors },
    } = useForm();

    useEffect(() => {
        (async () => {
            try {
                await Promise.all([fetchBooks(), fetchAuthors()]);
            } catch (err) {
                console.error(err);
                showAlert("Error loading data", "error");
            }
        })();
    }, []);

    const fetchBooks = async () => {
        try {
            const res = await api.get("/books");
            setBooks(res.data.data);
        } catch (err) {
            console.error(err);
            showAlert("Error loading books", "error");
        }
    };

    const fetchAuthors = async () => {
        try {
            const res = await api.get("/authors");
            setAuthors(res.data.data);
        } catch (err) {
            console.error(err);
            showAlert("Error loading authors", "error");
        }
    };

    const showAlert = (msg, type) => {
        setAlert({ msg, type });
        setTimeout(() => setAlert(null), 3000);
    };

    const handleDelete = async (id) => {
        if (!confirm("Are you sure you want to delete this book?")) return;
        try {
            await api.delete(`/books/${id}`);
            setBooks((prev) => prev.filter((b) => b.id !== id));
            showAlert("Book deleted successfully", "success");
        } catch (err) {
            console.error(err);
            showAlert("Error deleting book", "error");
        }
    };

    const handleEdit = (book) => {
        setEditingBook(book);
        reset({
            title: book.title || "",
            author_id: book.author_id || "",
            isbn: book.isbn || "",
            publication_year: book.publication_year || "",
            available: book.available,
        });
        setShowModal(true);
    };

    const handleCreate = () => {
        setEditingBook(null);
        reset({
            title: "",
            author_id: "",
            isbn: "",
            publication_year: "",
            available: true,
        });
        setShowModal(true);
    };

    const onSubmit = async (data) => {
        try {
            let res;
            if (editingBook) {
                res = await api.put(`/books/${editingBook.id}`, data);
                setBooks((prev) =>
                    prev.map((b) => (b.id === editingBook.id ? res.data.data : b))
                );
                showAlert("Book updated successfully", "success");
            } else {
                res = await api.post("/books", data);
                setBooks((prev) => [...prev, res.data.data]);
                showAlert("Book created successfully", "success");
            }
            setShowModal(false);
        } catch (err) {
            console.error(err);
            const backendMessage =
                err.response?.data?.message ||
                err.response?.data?.error ||
                "Unknown error";
            showAlert(`Error saving book: ${backendMessage}`, "error");
        }
    };

    const filteredBooks = books.filter(
        (b) =>
            b.title.toLowerCase().includes(search.toLowerCase()) ||
            b.author.toLowerCase().includes(search.toLowerCase()) ||
            b.isbn.toLowerCase().includes(search.toLowerCase())
    );

    const totalPages = Math.ceil(filteredBooks.length / itemsPerPage);
    const paginatedBooks = filteredBooks.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage
    );

    const handlePageChange = (page) => setCurrentPage(page);

    return (
        <div className="bg-neutral-900 border border-neutral-800 p-6 rounded-2xl shadow-lg relative">
            {/* Alert */}
            {alert && (
                <div
                    className={`fixed top-10 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl text-sm font-medium shadow-lg
                ${
                        alert.type === "success"
                            ? "bg-emerald-800/70 text-emerald-100 border border-emerald-500/40"
                            : "bg-red-500/70 text-neutral-100 border border-red-500/40"
                    }`}
                >
                    {alert.msg}
                </div>
            )}

            <div className="flex justify-between items-center mb-6">
                <h3 className="text-xl font-semibold text-cyan-400 tracking-tight">
                    Books
                </h3>
                <div className="flex items-center gap-3">
                    <input
                        type="text"
                        placeholder="Search book..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg
                                   text-neutral-200 text-sm focus:outline-none focus:border-cyan-400
                                   placeholder-neutral-500"
                    />
                    <button
                        onClick={handleCreate}
                        className="px-4 py-2 rounded-lg text-sm font-medium transition-all
                                   bg-gradient-to-r from-cyan-500 to-emerald-400 text-neutral-900
                                   hover:scale-105 hover:shadow-[0_0_12px_rgba(6,182,212,0.4)]"
                    >
                        + Add Book
                    </button>
                </div>
            </div>

            {/* Table */}
            <div className="overflow-x-auto rounded-lg border border-neutral-800">
                <table className="w-full border-collapse">
                    <thead className="bg-neutral-700 text-neutral-200 text-sm uppercase">
                    <tr>
                        <th className="py-3 px-4 text-left font-medium">Title</th>
                        <th className="py-3 px-4 text-left font-medium">Author</th>
                        <th className="py-3 px-4 text-left font-medium">ISBN</th>
                        <th className="py-3 px-4 text-left font-medium">Publication Year</th>
                        <th className="py-3 px-4 text-center font-medium">Available</th>
                        <th className="py-3 px-4 text-center font-medium">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {paginatedBooks.map((b) => (
                        <tr
                            key={b.id}
                            className="border-t border-neutral-800 hover:bg-neutral-800/60 transition-colors"
                        >
                            <td className="py-3 px-4 text-neutral-100 text-sm">{b.title}</td>
                            <td className="py-3 px-4 text-neutral-400 text-sm">{b.author}</td>
                            <td className="py-3 px-4 text-neutral-300 text-sm">{b.isbn}</td>
                            <td className="py-3 px-4 text-neutral-400 text-sm">{b.publication_year}</td>
                            <td className="py-3 px-4 text-center">
                                    <span
                                        className={`px-2 py-1 rounded-lg text-xs font-medium ${
                                            b.available
                                                ? "bg-emerald-500/20 text-emerald-400 border border-emerald-500/40"
                                                : "bg-red-500/20 text-red-400 border border-red-500/40"
                                        }`}
                                    >
                                        {b.available ? "Yes" : "No"}
                                    </span>
                            </td>
                            <td className="py-3 px-4 text-center">
                                <button
                                    onClick={() => handleEdit(b)}
                                    className="text-cyan-400 hover:text-cyan-300 mx-2 text-sm font-medium"
                                >
                                    Edit
                                </button>
                                <button
                                    onClick={() => handleDelete(b.id)}
                                    className="text-red-400 hover:text-red-300 mx-2 text-sm font-medium"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    ))}
                    {paginatedBooks.length === 0 && (
                        <tr>
                            <td
                                colSpan="6"
                                className="py-6 text-center text-neutral-500 italic"
                            >
                                No books found.
                            </td>
                        </tr>
                    )}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            <div className="flex justify-center items-center mt-6 space-x-2">
                {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                    <button
                        key={page}
                        onClick={() => handlePageChange(page)}
                        className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-all
                            ${
                            currentPage === page
                                ? "bg-gradient-to-r from-cyan-500 to-emerald-400 text-neutral-900"
                                : "bg-neutral-800 text-neutral-400 hover:text-cyan-400 hover:bg-neutral-700"
                        }`}
                    >
                        {page}
                    </button>
                ))}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
                    <div className="bg-neutral-900 border border-neutral-800 p-6 rounded-2xl shadow-xl w-full max-w-md">
                        <h3 className="text-lg font-semibold text-cyan-400 mb-4">
                            {editingBook ? "Edit Book" : "Add Book"}
                        </h3>
                        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                            {/* Title */}
                            <div>
                                <input
                                    type="text"
                                    placeholder="Title"
                                    {...register("title", { required: "Title is required" })}
                                    className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-200 text-sm focus:border-cyan-400 outline-none"
                                />
                                {errors.title && (
                                    <p className="text-red-500 text-xs mt-1">{errors.title.message}</p>
                                )}
                            </div>

                            {/* Author */}
                            <div>
                                <select
                                    {...register("author_id", { required: "Author is required" })}
                                    className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-200 text-sm focus:border-cyan-400 outline-none"
                                >
                                    <option value="">Select Author</option>
                                    {authors.map((author) => (
                                        <option key={author.id} value={author.id}>
                                            {author.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.author_id && (
                                    <p className="text-red-500 text-xs mt-1">{errors.author_id.message}</p>
                                )}
                            </div>

                            {/* ISBN */}
                            <div>
                                <input
                                    type="text"
                                    placeholder="ISBN"
                                    {...register("isbn")}
                                    className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-200 text-sm focus:border-cyan-400 outline-none"
                                />
                            </div>

                            {/* Year */}
                            <div>
                                <input
                                    type="number"
                                    placeholder="Year"
                                    {...register("publication_year", {
                                        min: { value: 0, message: "Year must be positive" },
                                    })}
                                    className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-neutral-200 text-sm focus:border-cyan-400 outline-none"
                                />
                                {errors.publication_year && (
                                    <p className="text-red-500 text-xs mt-1">
                                        {errors.publication_year.message}
                                    </p>
                                )}
                            </div>

                            {/* Available */}
                            <label className="flex items-center gap-2 text-sm text-neutral-300">
                                <input
                                    type="checkbox"
                                    {...register("available")}
                                    className="accent-cyan-400"
                                />
                                Available
                            </label>

                            {/* Buttons */}
                            <div className="flex justify-end space-x-3 mt-6">
                                <button
                                    type="button"
                                    onClick={() => setShowModal(false)}
                                    className="px-4 py-2 rounded-lg text-sm font-medium bg-neutral-800 text-neutral-400 hover:text-cyan-400 transition"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    className="px-4 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-cyan-500 to-emerald-400 text-neutral-900 hover:scale-105 transition"
                                >
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

