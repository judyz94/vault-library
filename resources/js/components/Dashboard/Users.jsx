import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import api from "../../api/api";

export default function UsersTable() {
    const [users, setUsers] = useState([]);
    const [search, setSearch] = useState("");
    const [currentPage, setCurrentPage] = useState(1);
    const [alert, setAlert] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [editingUser, setEditingUser] = useState(null);
    const itemsPerPage = 5;

    const {
        register,
        handleSubmit,
        reset,
        formState: { errors },
    } = useForm();

    useEffect(() => {
        fetchUsers()
            .then(() => {})
            .catch((err) => console.error(err));
    }, []);

    const fetchUsers = async () => {
        try {
            const res = await api.get("/users");
            setUsers(res.data.data);
        } catch (err) {
            console.error(err);
            showAlert("Error loading users", "error");
        }
    };

    const showAlert = (msg, type) => {
        setAlert({ msg, type });
        setTimeout(() => setAlert(null), 4000);
    };

    const handleDelete = async (id) => {
        if (!confirm("Are you sure you want to delete this user?")) return;
        try {
            await api.delete(`/users/${id}`);
            setUsers(users.filter((u) => u.id !== id));
            showAlert("User deleted successfully", "success");
        } catch (err) {
            console.error(err);
            showAlert("Error deleting user", "error");
        }
    };

    const handleEdit = (user) => {
        setEditingUser(user);
        reset({
            name: user.name,
            email: user.email,
            password: user.password,
            password_confirmation: user.password_confirmation,
            role: user.role,
            library_id: user.library_id,
        });
        setShowModal(true);
    };

    const handleCreate = () => {
        setEditingUser(null);
        reset({ name: "", email: "", password: "", password_confirmation: "", role: "", library_id: "" });
        setShowModal(true);
    };


    const onSubmit = async (data) => {
        try {
            if (editingUser) {
                const res = await api.put(`/users/${editingUser.id}`, data);
                setUsers(users.map((u) => (u.id === editingUser.id ? res.data.data : u)));
                showAlert("User updated successfully", "success");
            } else {
                const res = await api.post("/users", data);
                setUsers([...users, res.data.data]);
                showAlert("User created successfully", "success");
            }
            setShowModal(false);
        } catch (err) {
            console.error(err);
            const backendMessage =
                err.response?.data?.message ||
                err.response?.data?.error ||
                "Error saving user";
            showAlert(`Error saving user:  ${backendMessage}`, "error");
        }
    };

    const filteredUsers = users.filter(
        (u) =>
            u.name.toLowerCase().includes(search.toLowerCase()) ||
            u.email.toLowerCase().includes(search.toLowerCase())
    );

    const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
    const paginatedUsers = filteredUsers.slice(
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
                <h3 className="text-xl font-semibold text-cyan-400 tracking-tight">Users</h3>
                <div className="flex items-center gap-3">
                    <input
                        type="text"
                        placeholder="Search user..."
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
                        + Add User
                    </button>
                </div>
            </div>

            {/* Table */}
            <div className="overflow-x-auto rounded-lg border border-neutral-800">
                <table className="w-full border-collapse">
                    <thead className="bg-neutral-700 text-neutral-200 text-sm uppercase">
                    <tr>
                        <th className="py-3 px-4 text-left font-medium">Name</th>
                        <th className="py-3 px-4 text-left font-medium">Email</th>
                        <th className="py-3 px-4 text-left font-medium">Library ID</th>
                        <th className="py-3 px-4 text-left font-medium">Role</th>
                        <th className="py-3 px-4 text-center font-medium">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {paginatedUsers.map((u) => (
                        <tr
                            key={u.id}
                            className="border-t border-neutral-800 hover:bg-neutral-800/60 transition-colors"
                        >
                            <td className="py-3 px-4 text-neutral-100 text-sm">{u.name}</td>
                            <td className="py-3 px-4 text-neutral-400 text-sm">{u.email}</td>
                            <td className="py-3 px-4 text-cyan-400 text-sm font-medium">
                                {u.library_id}
                            </td>
                            <td className="py-3 px-4 text-emerald-400 text-sm font-medium">
                                {u.role}
                            </td>
                            <td className="py-3 px-4 text-center">
                                <button
                                    onClick={() => handleEdit(u)}
                                    className="text-cyan-400 hover:text-cyan-300 mx-2 text-sm font-medium"
                                >
                                    Edit
                                </button>
                                <button
                                    onClick={() => handleDelete(u.id)}
                                    className="text-red-400 hover:text-red-300 mx-2 text-sm font-medium"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    ))}
                    {paginatedUsers.length === 0 && (
                        <tr>
                            <td
                                colSpan="6"
                                className="py-6 text-center text-neutral-500 italic"
                            >
                                No users found.
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
                            {editingUser ? "Edit User" : "Add User"}
                        </h3>

                        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4 text-sm text-neutral-200">
                            {/* Name */}
                            <input
                                type="text"
                                placeholder="Name"
                                {...register("name", {required: "Name is required"})}
                                className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg
                           focus:border-cyan-400 outline-none"
                            />
                            {errors.name && <p className="text-red-500 text-xs">{errors.name.message}</p>}

                            {/* Email */}
                            <input
                                type="email"
                                placeholder="Email"
                                {...register("email", {
                                    required: "Email is required",
                                    pattern: {value: /^\S+@\S+$/i, message: "Invalid email format"},
                                })}
                                className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg
                           focus:border-cyan-400 outline-none"
                            />
                            {errors.email && <p className="text-red-500 text-xs">{errors.email.message}</p>}

                            {!editingUser && (
                                <>

                                    {/* Password */}
                                    <input
                                        type="password"
                                        placeholder="••••••••"
                                        {...register("password", {
                                            required: "Password is required",
                                            minLength: {
                                                value: 8,
                                                message: "Password must be at least 8 characters",
                                            },
                                        })}
                                        className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg
                                        focus:border-cyan-400 outline-none"
                                    />
                                    {errors.password && (
                                        <p className="text-red-500 text-xs">{errors.password.message}</p>
                                    )}

                                    {/* Password confirmation */}
                                    <input
                                        type="password"
                                        placeholder="Repeat password"
                                        {...register("password_confirmation", {
                                            required: "Please confirm your password",
                                            validate: (value, formValues) =>
                                                value === formValues.password || "Passwords do not match",
                                        })}
                                        className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg
                                        focus:border-cyan-400 outline-none"
                                    />
                                    {errors.password_confirmation && (
                                        <p className="text-red-500 text-xs">
                                            {errors.password_confirmation.message}
                                        </p>
                                    )}
                                </>
                            )}

                            {/* Library ID */}
                            <input
                                type="text"
                                placeholder="Library ID"
                                {...register("library_id", {
                                    required: "Library ID is required",
                                    pattern: {
                                        value: /^[A-Za-z0-9_-]+$/,
                                        message: "Only letters, numbers, dashes and underscores allowed",
                                    },
                                })}
                                className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg
                           focus:border-cyan-400 outline-none"
                            />
                            {errors.library_id && (
                                <p className="text-red-500 text-xs">{errors.library_id.message}</p>
                            )}

                            {/* Role */}
                            <select
                                {...register("role", {required: "Role is required"})}
                                className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg
                           focus:border-cyan-400 outline-none"
                                defaultValue=""
                            >
                                <option value="" disabled>
                                    Select Role
                                </option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                            {errors.role && <p className="text-red-500 text-xs">{errors.role.message}</p>}

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
                                    className="px-4 py-2 rounded-lg text-sm font-medium
                             bg-gradient-to-r from-cyan-500 to-emerald-400
                             text-neutral-900 hover:scale-105 transition"
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
